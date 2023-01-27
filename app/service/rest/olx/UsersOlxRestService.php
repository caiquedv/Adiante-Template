<?php

use Adianti\Database\TTransaction;
use Adianti\Service\AdiantiRecordService;

class UsersOlxRestService extends AdiantiRecordService
{
    const DATABASE      = 'olx';
    const ACTIVE_RECORD = 'UserOlxApi';

    public function handler($param)
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if (isset($param['name']) && $method != 'PUT') { // signUp
            $param['password'] = md5($param['password']);
            return ['token' => $this->genToken($param)];
        }

        TTransaction::open(self::DATABASE);
        if (!isset($param['token'])) { // signIn        
            $user = UserOlxApi::doLogin($param['email']);
        } else { // get user info
            $param['filters'] = [['token', '=', $param['token']]];
            $user = parent::loadAll($param);
        }
        TTransaction::close();

        if ($user) {
            if ($method != 'PUT' && isset($param['password']) && hash_equals($user['password'], md5($param['password']))) {
                return ['token' => $this->genToken($user)];
            }
            if (isset($param['filters']) && $method != 'PUT') {
                TTransaction::open(self::DATABASE);
                
                $adsUser = AdsOlxApi::getAds(['user_id' => $user[0]['id']]);

                TTransaction::close();
                
                return [ $user[0] + ['ads' => $adsUser]];
            } else {
                if (isset($param['name']) && $param['name'] != '') $user[0]['name'] = $param['name'];
                if (isset($param['email']) && $param['email'] != '') $user[0]['email'] = $param['email'];
                if (isset($param['state']) && $param['state'] != '') $user[0]['state'] = $param['state'];
                if (isset($param['password']) && $param['password'] != '') $user[0]['password'] = md5($param['password']);

                // return ['user' => $this->genToken($user[0])];
                return ['user' => parent::handle($user[0])['token']];
            }
        }
    }

    private function genToken($param)
    {
        $param['token'] = md5($param['name'] . date('h:i:s') . random_int(1, 5));


        return parent::handle($param)['token'];
    }
}
