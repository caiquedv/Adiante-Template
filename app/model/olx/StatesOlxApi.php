<?php

use Adianti\Database\TRecord;

class StatesOlxApi extends TRecord
{
    const TABLENAME = 'states';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
    }
}
