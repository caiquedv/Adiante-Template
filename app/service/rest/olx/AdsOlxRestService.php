<?php

use Adianti\Service\AdiantiRecordService;

class AdsOlxRestService extends AdiantiRecordService
{
    const DATABASE      = 'olx';
    const ACTIVE_RECORD = 'AdsOlxApi';

    public function ping($param)
    {
        return 'ok';
    }
}
