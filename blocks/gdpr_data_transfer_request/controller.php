<?php

namespace Concrete\Package\Gdpr\Block\GdprDataTransferRequest;

use A3020\Gdpr\DataTransfer\RequestRepository;
use A3020\Gdpr\Event\DataTransferRequest;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Http\Request;
use Concrete\Core\User\User;

class Controller extends BlockController
{
    protected $btInterfaceWidth = '450';
    protected $btInterfaceHeight = '400';
    protected $btTable = 'btGdprDataTransferRequest';
    protected $btWrapperClass = 'ccm-ui';
    protected $btDefaultSet = 'form';
    protected $btCacheBlockRecord = true;

    // Because the block should't be visible if the user isn't logged in
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /* @var $error \Concrete\Core\Error\ErrorList\ErrorList */
    protected $error;

    /** @var bool */
    protected $includeFiles;

    public function on_start()
    {
        $this->error = $this->app->make('helper/validation/error');

        $user = new User();

        $this->set('user', $user);
        $this->set('token', $this->app->make('token'));
        $this->set('errors', $this->error);
        $this->set('hasPendingRequest', $this->hasPendingRequest($this->get('user')));
    }

    public function getBlockTypeName()
    {
        return t('GDPR - Data Transfer Request');
    }

    public function getBlockTypeDescription()
    {
        return t('Triggers an event that creates a Data Transfer Request for the current user.');
    }

    public function view()
    {

    }

    public function action_submit($bId = false)
    {
        $user = new User();
        if (!$user->isLoggedIn()){
            return $this->view();
        }

        if (!Request::isPost()) {
            return $this->view();
        }

        if ($bId !== $this->bID) {
            return $this->view();
        }

        if (!$this->app->make('token')->validate('gdpr.data_transfer_request')) {
            $this->error->add(t('Invalid token'));
        }

        if (!$this->error->has() && !$this->hasPendingRequest($user)) {
            $this->createDataTransferRequest($user);
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function hasPendingRequest(User $user)
    {
        if (!$user->isLoggedIn()){
            return false;
        }

        /** @var RequestRepository $requestRepository */
        $requestRepository = $this->app->make(RequestRepository::class);

        return $requestRepository->hasUnprocessedRequests($user->getUserInfoObject()->getEntityObject());
    }

    /**
     * @param User $user
     */
    private function createDataTransferRequest(User $user)
    {
        $event = new DataTransferRequest($user->getUserInfoObject()->getEntityObject());

        if (!$this->includeFiles) {
            $event->skipFiles();
        }

        $this->app['director']->dispatch('on_gdpr_data_transfer_request', $event);

        $this->set('sent', true);
    }
}
