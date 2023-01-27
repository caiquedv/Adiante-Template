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

        // if (isset($param['user_id']) && $param['user_id']) {
        //     array_push($param['filters'], ['user_id', '=', "{$param['user_id']}"]);
        // }
        // return $param;

        if (isset($param['token']) && $param['token']) {
            TTransaction::open(self::DATABASE);
            
            $user =  UserOlxApi::getUser($param['token']);
            // return $user;

            $param['user_id'] = $user['id'];
            $param['state'] = $user['state'];

            TTransaction::close();
        }

        return parent::handle($param);
    }
}
