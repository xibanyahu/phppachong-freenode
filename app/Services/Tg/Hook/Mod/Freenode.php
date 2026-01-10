<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\Tg\Hook\Dog;
//use App\Services\TomTool\Flow;
use App\Services\TomTool\ThrowableHandler;
//use App\Services\Cron\FreenodeMerge as CronFreenodeMerge;
use App\Models\FreenodePool as ModelFreenodePool;
use App\Services\TomTool\HttpV2;
use App\Services\TomTool\Map;
use App\Models\SiteMap as ModelSiteMap;
use App\Services\TomTool\Telegram\Slave as TeleSlave;

class Freenode extends Base {

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelFreenodePool());
        $this->oDog->_setDogName("Freenode");
        $this->oDog->_setJsonField(["content"]);
//        $this->oDog->_setStrField(["content"]);
        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
    public function show($aParam)
    {
        $sSiteCode = $aParam["aArg"][1] ?? "cfn";
        $sClient = $aParam["aOption"][1] ?? "clash";
        
        if ($sSiteCode === "?") {
            return "/show#__[client:clash] [siteCodeShort]";
        }
        
        $oSite = ModelSiteMap::where("code_short", $sSiteCode)->first();
        
        if (!$oSite) {
            return "没有这个site";
        }
        
        $sClientSfx = Map::fnClientToSfx($sClient);
        
        $sFileName = date("Ymd")."-".$sClient.".".$sClientSfx;
        $sFileUrl = "https://".$oSite->url."/sub/".$sFileName;
//        $sFileUrl = "https://clashv2rayfree.com/sub/".$sFileName; // test
        
        $oFlow = TeleSlave::start("admin", "admin")
            ->enableSlaveQueue(false)
            ->enableAsync(false)
            ->enableDetail(false)
            ->setFileUrl($sFileUrl, $sSiteCode."的".$sClient.".txt")
            ->send();
        
        if ($oFlow->isFail()) {
            var_dump($oFlow->getResult());
            exit;
        }
        
    }
    
}
