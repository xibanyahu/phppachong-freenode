<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

//use App\Models\Article;
//use App\Services\TomTool\Http;
//use App\Services\TG\Http as TGHttp;
//use App\Models\Ship as ModelShip;
use App\Models\Option as ModelOption;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

final class FreenodeController
{

    public function sub($sSiteCode, $sClient, $sDate = "")
    {
        header('Content-Type: application/x-yaml; charset=utf-8');
        $sDirBase = $_ENV["dir_base"];
        
//        $aData = $request->json()->all();
        
//        $sSiteCode = $sSiteCode ?? "base";
//        $sClient = $aData["client"] ?? "";
        $sDate = $sDate ?? date("Y-m-d");
        
        //// *test
        $sDate = "2025-12-22";
//        $sClient = "clash";
        //// test end

        $aClient = [];
        
        if ($sClient == "_all") {
            $sFnClient = ModelOption::_hit("freenode_client");
            $aFnClient = explode(",", $sFnClient);
            foreach ($aFnClient as $sFnClient) {
                $aClient[] = $sFnClient;
            }
        } else {
            $aClient[] = $sClient;
        }

        $aContent = [];
        foreach ($aClient as $sClient) {
            $sFilePath = $this->pathReal($sSiteCode, $sClient, $sDate);
            $sFileContent = file_get_contents($sFilePath); // data
            $aContent[$sClient] = $sFileContent;
        }
        
        $iContentCount = count($aContent);

        if ($iContentCount == 1) {
            $k = array_key_first($aContent);
            $sContent = $aContent[$k];
            
            if ($k == "clash") {
                header('Content-Type: application/x-yaml; charset=utf-8');
            } else {
                header('Content-Type: text/plain; charset=utf-8');
            }
            
        } else {
            $sContent = json_encode($aContent);
        }
        
        echo $sContent;
        exit;
    }
    
    private function pathReal($sSiteCode, $sClient, $sDate, $iDeep = 0)
    {
        if ($iDeep > 30) {
            return false;
        }

        $sDirBase = $_ENV["dir_base"];
        $sFilePath = $sDirBase."public/freenode/merge/".$sSiteCode."/".$sClient."/".$sDate.".txt";

        if (file_exists($sFilePath)) {
            return $sFilePath;
        } else {
            $oData = new \DateTime($sDate);
            $oData->modify('-1 day');
            $sDatePrev = $oData->format('Y-m-d');

            return $this->pathReal($sSiteCode, $sClient, $sDatePrev, ++$iDeep);
        }
    }

    
}
