<?php

use Adianti\Database\TRecord;

class AdsOlxApi extends TRecord
{
    const TABLENAME = 'ads';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('status');
        parent::addAttribute('user_id');
        parent::addAttribute('state');
        parent::addAttribute('title');
        parent::addAttribute('category');
        parent::addAttribute('price');
        parent::addAttribute('price_negotiable');
        parent::addAttribute('description');
        parent::addAttribute('views');
    }
}
