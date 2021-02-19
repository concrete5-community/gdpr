<?php

namespace A3020\Gdpr\Ajax\DataTransfer;

use A3020\Gdpr\Controller\AjaxController;
use A3020\Gdpr\DataTransfer\RequestRepository;
use A3020\Gdpr\Entity\DataTransferRequest;
use Concrete\Core\Http\ResponseFactory;

class Requests extends AjaxController
{
    /** @var RequestRepository */
    protected $requestRepository;

    public function on_start()
    {
        parent::on_start();

        $this->requestRepository = $this->app->make(RequestRepository::class);
    }

    public function view()
    {
        $json['data'] = $this->getRecords();

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    /**
     * Return a list data transfer requests
     *
     * @return array
     */
    private function getRecords()
    {
        $records = [];

        foreach ($this->requestRepository->findAll() as $request) {
            /** @var DataTransferRequest $request */
            $user = $request->getUser();
            if (!$user) {
                continue;
            }

            $approvedAt = $request->getApprovedAt();
            $mailedAt = $request->getMailedAt();

            $records[] = [
                'requested_at' => $request->getRequestedAt()->format('Y/m/d'),
                'mailed_at' => $mailedAt ? $mailedAt->format('Y/m/d') : null,
                'approved_at' => $approvedAt ? $approvedAt->format('Y/m/d') : null,
                'user_name' => $user->getUserName(),
            ];
        }

        return $records;
    }
}
