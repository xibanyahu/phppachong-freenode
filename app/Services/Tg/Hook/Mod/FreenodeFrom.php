<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\Tg\Hook\Dog;
use App\Services\TomTool\Flow;
use App\Services\TomTool\ThrowableHandler;
//use App\Services\Cron\FreenodeMerge as CronFreenodeMerge;
//use App\Models\FreenodePool as ModelFreenodePool;
use App\Models\CrawlFreenode as ModelCrawlFreenode;
use App\Services\TomTool\HttpV2;

class FreenodeFrom extends Base {

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelCrawlFreenode());
        $this->oDog->_setDogName("crawlFreenode");
        $this->oDog->_setJsonField(["url_file", "file_real"]);
//        $this->oDog->_setStrField(["content"]);
        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
}
