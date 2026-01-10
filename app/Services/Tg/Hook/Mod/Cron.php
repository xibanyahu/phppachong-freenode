<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\TomTool\Http;
use App\Services\Cron as ServicesCron;
use App\Services\Cron\HttpQueue as CronHttpQueue;

class Cron extends Base {
    
    public function httpQueuePop()
    {
        $r = CronHttpQueue::pop();
        return $r;
    }
    
    public function test($aParam)
    {
        $sType = $aParam["aOption"][1] ?? "i";
        $sValue = $aParam["aArg"][1] ?? "03";
        
        if (!$sType) {
            return "需要his之一";
        }
        
        if (!$sValue) {
            return "需要value";
        }
        
        if ($sValue == "?") {
            return "#test__[his] [value]";
        }
        
        $oServicesCron = new ServicesCron();
        
        $oServicesCron->setTestH('06');
        $oServicesCron->setTestI('03');
        $oServicesCron->setTestS('00');
        
        if ($sType == "h") {
            $oServicesCron->setTestH($sValue);
        } else if ($sType == "i") {
            $oServicesCron->setTestI($sValue);
        } else if ($sType == "s") {
            $oServicesCron->setTestS($sValue);
        }
        
        return $oServicesCron->run();
    }
    
    public function testOld($aParam)
    {
        $sType = $aParam["aArg"][1] ?? false;
        $sValue = $aParam["aArg"][2] ?? false;
        
        $oHttp = new Http();
        $oHttp->sMethod = "get";
        $oHttp->bIsJson = true;
        $oHttp->sToUrl = env("APP_URL")."/cron";
        
        if ($sType && $sValue) {
            $oHttp->aData = ["k" => $sType, "v" => $sValue];
        }
        
        $r = $oHttp->send();
        
        return $r;
    }
    
}
