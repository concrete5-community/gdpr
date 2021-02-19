<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Check\CheckRepository;
use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;

final class Checklist extends DashboardController
{
    public function view()
    {
        $this->set('checks', $this->getChecks());
    }

    public function check()
    {
        /** @var CheckRepository $repository */
        $repository = $this->app->make(CheckRepository::class);

        /** @var \A3020\Gdpr\Entity\Check $entity */
        $entity = $repository->find($this->post('id'));

        if (!$entity) {
            return $this->app->make(ResponseFactory::class)->json([
                'success' => false,
                'error' => t('Check not found'),
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($this->post('checked')) {
            $entity->markAsChecked();
        } else {
            $entity->markAsUnChecked();
        }

        $repository->save($entity);
        $repository->flush();

        return $this->app->make(ResponseFactory::class)->json([
            'success' => true,
            'checked' => $entity->isChecked(),
        ]);
    }

    private function getChecks()
    {
        /** @var CheckRepository $repository */
        $repository = $this->app->make(CheckRepository::class);

        return $repository->findAll();
    }
}
