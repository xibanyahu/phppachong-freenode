<?php

declare(strict_types=1);

namespace App\Services\Cron;

//use App\Models\CrawlFreenode as ModelCrawlFreenode;
use App\Services\TomTool\Telegram\Slave as TeleSlave;
use App\Models\CrawlFreenode as ModelCrawlFreenode;
//use Symfony\Component\Yaml\Yaml;

final class FreenodeLog
{
    public static function fromLast()
    {
        $oFrom = ModelCrawlFreenode::where("status", 1)->get();
        
        tomd($oFrom->toArray());
        
        exit;
    }
    
}
