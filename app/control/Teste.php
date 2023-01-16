<?php

use Adianti\Control\TPage;
use Adianti\Widget\Form\TLabel;

class Teste extends TPage
{
    public function __construct()
    {
        parent::__construct();
        // $a > 5;
        parent::add(new TLabel('Teste'));
    }
}