<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cleanup;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\User\User;

final class OrphanedFiles extends DashboardController
{
    protected $maxResults = 300;

    public function view()
    {
        $this->set('maxResults', $this->maxResults);
        $this->set('orphanedFiles', $this->getOrphanedFiles());
    }

    public function bulk()
    {
        if (!$this->token->validate('gdpr.cleanup.orphaned_files.bulk')) {
            $this->flash('error', t('Invalid form token'));

            return Redirect::to('/dashboard/gdpr/cleanup/orphaned_files');
        }

        if ($this->post('action') === 'delete') {
            $this->flash('success', t('The selected files have been deleted.'));

            $this->deleteFiles($this->post('files', []));
        }

        if ($this->post('action') === 'reassign') {
            $this->flash('success', t('The selected files have been reassigned.'));

            $this->reassignFiles($this->post('files', []));
        }

        return Redirect::to('/dashboard/gdpr/cleanup/orphaned_files');
    }

    /**
     * Orphaned files don't have an 'author' set.
     *
     * This is because Doctrine (see File entity) sets the
     * author id to NULL when a user is deleted.
     *
     * @return array
     */
    private function getOrphanedFiles()
    {
        $files = [];

        $fl = new FileList();
        $fl->sortBy('fv.fvDateAdded');
        $query = $fl->getQueryObject();
        $query->andWhere('f.uID IS NULL');
        $query->setMaxResults($this->maxResults);

        foreach ($fl->getResults() as $file) {
            /** @var \Concrete\Core\Entity\File\File $file */
            $fv = $file->getVersion();

            $files[] = [
                'id' => $file->getFileID(),
                'url' => $fv->getURL(),
                'name' => $fv->getFileName(),
                'size' => $fv->getSize(),
                'type' => $fv->getDisplayType(),
                'modified_at' => $fv->getDateAdded(),
            ];
        }

        return $files;
    }

    /**
     * Delete multiple files at once
     *
     * @param int[] $fileIds
     * @throws \Exception
     */
    private function deleteFiles(array $fileIds)
    {
        foreach ($fileIds as $fileId) {
            $file = File::getByID($fileId);
            if ($file) {
                $file->delete();
            }
        }
    }

    private function reassignFiles(array $fileIds)
    {
        $u = User::getByUserID(USER_SUPER_ID);
        $user = $u->getUserInfoObject()->getEntityObject();

        foreach ($fileIds as $fileId) {
            $file = File::getByID($fileId);
            if (!$file) {
                continue;
            }

            $file->setUser($user);
        }
    }
}
