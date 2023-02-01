<?php

use Adianti\Database\TRecord;
use Adianti\Database\TTransaction;

class UserOlxApi extends TRecord
{
    const TABLENAME = 'users';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

    private $stateObj;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('email');
        parent::addAttribute('state');
        parent::addAttribute('password');
        parent::addAttribute('token');
    }

    public function set_stateObj(StatesOlxApi $object)
    {
        $this->stateObj = $object;
        $this->state = $object->id;
    }

    public function get_stateObj()
    {
        TTransaction::open('olx');

        if (empty($this->stateObj)) {
            $this->stateObj = new StatesOlxApi($this->state);
        }

        TTransaction::close();

        return $this->stateObj;
    }

    // static public function get_state($id = null)
    // {
    //     $state = StatesOlxApi::where('id', '=', $id)->first();
    //     if ($state) {
    //         return $state->data;
    //     }
    // }

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
