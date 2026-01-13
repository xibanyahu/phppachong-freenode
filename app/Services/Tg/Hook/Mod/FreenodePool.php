<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\Tg\Hook\Dog;
use App\Services\TomTool\Flow;
use App\Services\TomTool\ThrowableHandler;
use App\Services\Cron\FreenodeMerge as CronFreenodeMerge;
use App\Models\FreenodePool as ModelFreenodePool;
use App\Services\TomTool\HttpV2;

class FreenodePool extends Base {

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelFreenodePool());
        $this->oDog->_setDogName("FreenodePool");
        $this->oDog->_setJsonField(["content"]);
//        $this->oDog->_setStrField(["content"]);
        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
    public function showPool($aParam)
    {
        $sDate = $aParam["aArg"][1] ?? date("Y-m-d");
        $sVtype = $aParam["aOption"][1] ?? "custom";
        
        $sContentType = "content_".$sVtype;
        
        $aPool = ModelFreenodePool::where("date", $sDate)->first()->toArray();
        
        $sContent = $aPool[$sContentType];
        
        $aContent = json_decode($sContent, true);
        
        return $this->toJson($aContent);
    }
    
    public function show($aParam)
    {
        $sSiteCode = $aParam["aOption"][1] ?? "cfn";
        $sClient = $aParam["aOption"][2] ?? "v2ray";
        $sDate = $aParam["aArg"][1] ?? date("Y-m-d");
        
        
        if ($sDate == "?") {
            return "show__[siteCode]__[client] [Y-m-d]";
        }
        
        $sDirBase = $_ENV["dir_base"];
        
        $sFilePath = $sDirBase."public/freenode/merge/{$sSiteCode}/{$sClient}/{$sDate}.txt";
        
        $sContent = file_get_contents($sFilePath);

        $sFeed = "";
        if ($sClient == "v2ray") {
            $sFeed = CronFreenodeMerge::v2rayFeedToStr($sContent);
//            $sContent = base64_decode($sContent);
//            $aContent = explode("\n", $sContent);
//            foreach ($aContent as $sNode) {
//                $aTmp = explode("#", $sNode);
//                $sName = $aTmp[1];
//                $sName = urldecode($sName);
//                $sNode = $aTmp[0]."#".$sName;
//                $sFeed .= $sNode."\n";
//            }
        } else if ($sClient == "clash") {
            $sFeed = $sContent;
        }
        
        return $sFeed;
    }
    
}
