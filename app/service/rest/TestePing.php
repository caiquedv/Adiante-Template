<?php

use Adianti\Service\AdiantiRecordService;

class TestePing extends AdiantiRecordService
{
    const DATABASE      = 'olx';
    const ACTIVE_RECORD = 'StatesOlxApi';

    public function ping()
    {
        return json_encode(array('pong' => true));
    }
}
