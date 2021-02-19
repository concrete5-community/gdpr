<?php

namespace A3020\Gdpr\Form;

class Helper
{
    /**
     * @param string $data
     *
     * @return array
     */
    public function convertTextArea($data)
    {
        $data = explode("\n", str_replace("\r", '', $data));

        return array_map('trim', $data);
    }
}
