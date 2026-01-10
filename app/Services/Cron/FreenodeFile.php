<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Models\FreenodePool as ModelFreenodePool;
use App\Models\CrawlFreenode as ModelCrawlFreenode;
use App\Models\SiteMap as ModelSiteMap;
use App\Models\Option as ModelOption;
use App\Services\TomTool\Telegram\Slave as TeleSlave;

final class FreenodeFile
{
    
    private static $aBaseProtocol = [
        "clash",
        "v2ray"
    ];
    
    public static function clear()
    {
        echo "\nFreenodeFile::clear - 开始\n";
        
        self::clear_from();
        self::clear_merge__fn_a();
        
    }
    
    public static function clear_merge__fn_a()
    {
        $sDirBase = $_ENV["dir_base"];
        
        $sPrevDay = 30;
        
        $sTimePrevX = strtotime("-$sPrevDay days");
        $sDatePrevX = date("Y-m-d", $sTimePrevX);
        
        $aFnSiteList = ModelSiteMap::where("group_code", "fn_a")->pluck("code")->toArray();
        $aFnSiteList[] = "base";
        
        $sFnClient = ModelOption::_hit("freenode_client-fn_a");
        $aFnClient = explode(",", $sFnClient);
        
        foreach ($aFnSiteList as $sFnSiteName) {
            foreach ($aFnClient as $sClient) {
                $sFileDir = $sDirBase."public/freenode/merge/".$sFnSiteName."/".$sClient;
                
                foreach (scandir($sFileDir) as $sFileName) {
                    if ($sFileName === '.' || $sFileName == '..') continue;
                    $sFilePath = $sFileDir.'/'.$sFileName;
                    
                    if (preg_match('/(\d{4}-\d{2}-\d{2})/', $sFileName, $matches)) {
                        $sDateCode = $matches[1];

                        $sFileDate = \DateTime::createFromFormat('Y-m-d', $sDateCode);
                        if ($sFileDate && $sFileDate->getTimestamp() < $sTimePrevX) {
                            if (unlink($sFilePath)) {
                                echo " - 已删除: $sFilePath\n";
                            } else {
                                echo " - 删除失败: $sFilePath\n";
                            }
                        }
                    }
                }
                echo " - fn的merge里的".$sFnSiteName."里的".$sClient."的file清理完成 \n";
            }
        }
    }
    
    private static function clear_from()
    {
        $sDirBase = $_ENV["dir_base"];
        
        $sPrevDay = 30;
        
        $sTimePrevX = strtotime("-$sPrevDay days");
        $sDateCodePrevX = date("Ymd", $sTimePrevX);
        
        $oFromList = ModelCrawlFreenode::where("status", 1)->get();
        
        foreach ($oFromList as $oFrom) {
            $aFileLast = json_decode($oFrom->file_real, true);
            $sFileDir = $sDirBase."public/freenode/from/".$oFrom->name;
            
            foreach (scandir($sFileDir) as $sFileName) {
                if ($sFileName === '.' || $sFileName == '..') continue;
                $sFilePath = $sFileDir.'/'.$sFileName;
                
                if (preg_match('/(\d{8})/', $sFileName, $matches)) {
                    $sDateCode = $matches[1]; // 提取到的日期字符串
                    $sFileDate = \DateTime::createFromFormat('Ymd', $sDateCode);
                    if ($sFileDate && $sFileDate->getTimestamp() < $sTimePrevX) {
                        if (unlink($sFilePath)) {
                            echo " - 已删除: $sFilePath\n";
                        } else {
                            echo " - 删除失败: $sFilePath\n";
                        }
                    }
                }
            }
            echo " - fn的from的".$oFrom->name."里的file清理完成 \n";
        }
    }
}
