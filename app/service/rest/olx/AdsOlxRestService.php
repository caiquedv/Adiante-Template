<?php

use Adianti\Database\TTransaction;
use Adianti\Service\AdiantiRecordService;

class AdsOlxRestService extends AdiantiRecordService
{
    const DATABASE      = 'olx';
    const ACTIVE_RECORD = 'AdsOlxApi';

    public function handler($param)
    {
        $param['filters'] = [];

        if (isset($param['q']) && $param['q']) {
            array_push($param['filters'], ['title', 'LIKE', "%{$param['q']}%"]);
        }

        if (isset($param['cat']) && $param['cat']) {
            array_push($param['filters'], ['category', '=', "{$param['cat']}"]);
        }

        if (isset($param['state']) && $param['state']) {
            array_push($param['filters'], ['state', '=', "{$param['state']}"]);
        }


        if (isset($param['token']) && $param['token']) {

            TTransaction::open(self::DATABASE);

            $user =  UserOlxApi::getUser($param['token']);

            $param['user_id'] = $user['id'];
            $param['state'] = $user['state'];

            TTransaction::close();
        }

        if (isset($_FILES['img']) && $_FILES['img']) {
            $file = $_FILES['img'];

            move_uploaded_file($file['tmp_name'], './app/images/adImages/' . $file['name']);

            $param['img'] = $file['name'];
        }

        $ad = parent::handle($param);

        // if (isset($ad[1])) {
            for ($i = 0; $i < count($ad); $i++) {
                $imgName = $ad[$i]['img'] ?? "default.jpg";

                $ad[$i]['img'] = "http://localhost/adianti/template/app/images/adImages/{$imgName}";
            }
        // } 

        return $ad;
    }

    public function handleWId($param)
    {
        $ad = parent::handle($param);

        $imgName = $ad['img'] ?? "default.jpg";

        $ad['img'] = "http://localhost/adianti/template/app/images/adImages/{$imgName}";

        return $ad;
    }
}
