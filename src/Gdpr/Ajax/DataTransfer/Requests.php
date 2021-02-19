<?php

namespace A3020\Gdpr\Ajax\DataTransfer;

use A3020\Gdpr\Controller\AjaxController;
use Concrete\Core\Http\ResponseFactory;

class Requests extends AjaxController
{
    public function view()
    {
        $json['data'] = $this->getRecords();

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    /**
     * Return a list of pages with blocks that might contain user data
     *
     * @return array
     */
    private function getRecords()
    {
        $records = [];


        return $records;
    }
}
