<?php

namespace A3020\Gdpr\DataTransfer\Queue;

use A3020\Gdpr\DataTransfer\RequestRepository;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use ZendQueue\Queue as ZendQueue;

class Create implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var RequestRepository
     */
    private $requestRepository;

    public function __construct(RequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function create(ZendQueue $q)
    {
        foreach ($this->requestRepository->findNotProcessed() as $request) {
            $q->send(json_encode([
                'id' => $request->getId(),
            ]));
        }
    }
}
