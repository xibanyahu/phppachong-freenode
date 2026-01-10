<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Models\SiteMap as ModelSiteMap;
use App\Services\TomTool\Telegram\Slave as TeleSlave;
use App\Services\TomTool\HttpV2;

final class FreenodeSync
{
    static function run()
    {
        echo "\nFeenodeSync::run() - 开始 \n";
        
        $sDirBase = $_ENV["dir_base"];
        
        $oFnSiteList = ModelSiteMap::where("group_code", "fn_a")->get();
        
        $aResultAll = [];
        foreach ($oFnSiteList as $oFnSite) {
            
            $aSiteConfig = json_decode($oFnSite->config, true);
            $aSiteConfigFnClient = $aSiteConfig["freenode_client"] ?? [];
            $aSiteApi = json_decode($oFnSite->api_path, true);
            $sSiteApiFreenodeReceive = $aSiteApi["freenode_receive"] ?? "";
            
            $aFnContent = [];
            foreach ($aSiteConfigFnClient as $aSiteConfigFnClientRow) {
                $sFilePath = $sDirBase."public/freenode/merge/".$oFnSite->code."/".$aSiteConfigFnClientRow."/".date("Y-m-d").".txt";

                if (file_exists($sFilePath)) {
                    $aFnContent[$aSiteConfigFnClientRow] = file_get_contents($sFilePath);
                } else {
                    $sMsg = "freenodeSync的run没有当天文件".$sFilePath;
                    TeleSlave::warn()->send($sMsg);
                }
            }
            
            $sApiUrl = "https://".$oFnSite->url."/".$sSiteApiFreenodeReceive;
            $sResult = HttpV2::make($sApiUrl, "post")->setDataA($aFnContent)->enableJsonSend()->send();

            $aResult = json_decode($sResult, true);
            
            if (!isset($aResult["is_done"]) || $aResult["is_done"] != true) {
                $sMsg = "freenodeSync::同步失败 ->".$oFnSite->name."->".$sResult;
                echo $sMsg;
                TeleSlave::warn()->setMsgS($sMsg)->send();
                continue;
            }
            
        }
        
        echo " - ok \n";
    }
}
