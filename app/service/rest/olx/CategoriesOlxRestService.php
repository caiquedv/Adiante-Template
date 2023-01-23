<?php

use Adianti\Service\AdiantiRecordService;

class CategoriesOlxRestService extends AdiantiRecordService
{
    const DATABASE      = 'olx';
    const ACTIVE_RECORD = 'CategoriesOlxApi';

    public function handler($param)
    {
        $dbCategories =  parent::handle($param);

        $categories = [];

        for ($i = 0; $i < count($dbCategories); $i++) {
            array_push(
                $categories,
                $dbCategories[$i] +
                    ['img' => "http://localhost/adianti/template/app/images/assets/{$dbCategories[$i]['slug']}.png"]
            );
        }

        return $categories;
    }
}