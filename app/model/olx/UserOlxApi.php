<?php

use Adianti\Database\TRecord;

class UserOlxApi extends TRecord
{
    const TABLENAME = 'users';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('email');
        parent::addAttribute('state');
        parent::addAttribute('password');
        parent::addAttribute('token');
    }

    static public function doLogin($email)
    {
        $user = UserOlxApi::where('email', '=', $email)->first();
        if ($user) {
            return $user->data;
        }
    }

    static public function getUser($token)
    {
        $user = UserOlxApi::where('token', '=', $token)->first();
        if ($user) {
            return $user->data;
        }
    }
}
