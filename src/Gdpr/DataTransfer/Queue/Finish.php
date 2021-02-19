<?php

namespace A3020\Gdpr\DataTransfer\Queue;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use ZendQueue\Queue as ZendQueue;

class Finish implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function finish(ZendQueue $q)
    {
        return t('All data transfer requests have been processed.');
    }
}
