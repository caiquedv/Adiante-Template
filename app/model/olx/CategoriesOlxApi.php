<?php

use Adianti\Database\TRecord;

class CategoriesOlxApi extends TRecord
{
    const TABLENAME = 'categories';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('slug');
    }
}