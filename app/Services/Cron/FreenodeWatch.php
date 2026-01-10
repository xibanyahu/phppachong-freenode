<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Models\SiteMap as ModelSiteMap;
use App\Services\TomTool\Telegram\Slave as TeleSlave;
use App\Services\TomTool\HttpV2;

final class FreenodeWatch
{
    static function run()
    {
        echo "\n\nFreenodeWatch::run() - 开始";
        
        $sDirBase = $_ENV["dir_base"];
        
        $oFnSiteList = ModelSiteMap::where("group_code", "fn_a")->get();
        
//        foreach ()
        
        echo "\n - ok";
    }
}
