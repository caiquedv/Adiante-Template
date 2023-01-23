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
            if ($method != 'PUT' && isset($param['password']) && $param['password'] === $user['password']) {
                return ['token' => $this->genToken($user)];
            }
            if (isset($param['filters']) && $method != 'PUT') {
                return $user;
            } else {
                if (isset($param['name']) && $param['name'] != '') $user[0]['name'] = $param['name'];
                if (isset($param['email']) && $param['email'] != '') $user[0]['email'] = $param['email'];
                if (isset($param['state']) && $param['state'] != '') $user[0]['state'] = $param['state'];
                if (isset($param['password']) && $param['password'] != '') $user[0]['password'] = $param['password'];
                return ['user' => parent::handle($user[0])];
            }
        }
    }

    private function genToken($param)
    {
        $param['token'] = md5($param['name'] . date('h:i:s')) . random_int(1, 5);

        return parent::handle($param)['token'];
    }
}