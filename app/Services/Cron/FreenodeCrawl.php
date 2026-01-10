<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Models\CrawlFreenode as ModelCrawlFreenode;
use App\Services\TomTool\Telegram\Slave as TeleSlave;
//use Symfony\Component\Yaml\Yaml;

final class FreenodeCrawl
{
    
    // 自有系
    // 必然获取到，但是密码会变，暂定每天8点变，意味着8袋内03分获取的是新密码
    private static function getFile___my($aFrom)
    {
        $aFileContent = [];
        
        foreach ($aFrom["url_file"] as $k => $sFileName) {
            
            $sUrlSend = "https://".$aFrom["url_base"].$aFrom["url_node"].$sFileName;
            
            $sFileContent = self::curl($sUrlSend) ?? "";

            if ($sFileContent) {
                $aFileContent[] = [
                    "type"  => $k,
                    "file_name" => $sFileName,
                    "file_content"  => $sFileContent
                ];
            }
            
        }
        
        return $aFileContent;
    }
    
    // 通用系，有就拿，没有就不拿，暂时没有date
    private static function getFile__common($aFrom)
    {
        $aFileContent = [];
        
        foreach ($aFrom["url_file"] as $k => $v) {
            
            $sFileName = $v;
            
            $sUrlSend = "https://".$aFrom["url_base"].$aFrom["url_node"].$sFileName;
            
            $sFileContent = self::curl($sUrlSend) ?? "";

            if (!$sFileContent) {
                return [];
            }
            
            if ($k == "v2ray") {
                $sFileContent = base64_decode($sFileContent);
            }
            
            if ($sFileContent) {
                $aFileContent[] = [
                    "type"  => $k,
                    "file_name" => $sFileName,
                    "file_content"  => $sFileContent
                ];
            }
            
        }
        
        return $aFileContent;
    }
    
    // stairnode系
    // ripaojiedian系，stairnode本源
    // 这个可能会没有，没有就不更新
    private static function getFile__ripaojiedian($aFrom)
    {
        $aFileContent = [];
        
        foreach ($aFrom["url_file"] as $k => $v) {
            
            $sFileName = str_replace("{{Ymd}}", date("Ymd"), $v);
            
            $sUrlSend = "https://".$aFrom["url_base"].$aFrom["url_node"].$sFileName;
            
            $sFileContent = self::curl($sUrlSend) ?? "";
//            tomd($sUrlSend);
//            tomd($sFileContent, 1);
            if (!$sFileContent) {
                return [];
            }
            
            if ($k == "v2ray") {
                $sFileContent = base64_decode($sFileContent);
            }
            
            if ($sFileContent) {
                $aFileContent[] = [
                    "type"  => $k,
                    "file_name" => $sFileName,
                    "file_content"  => $sFileContent
                ];
            }
            
        }
        
        return $aFileContent;
    }
    
    // v2rayshare系
    // 这必然有，和下一天一致说明没更新（但是好像改了，现在它是一次生成很多天）
    private static function getFile__v2rayshare($aFrom)
    {
        $aFileContent = [];
        $sTime = time();
        $sTimeNext = strtotime("+1 day", $sTime);
        
        $sUrlBase = $aFrom["url_base"];
        $sUrlNode = $aFrom["url_node"];
        $sUrlNode = str_replace("{{Y}}", date("Y"), $sUrlNode);
        $sUrlNode = str_replace("{{m}}", date("m"), $sUrlNode);
        $sUrlNodeNext = str_replace("{{Y}}", date("Y", $sTimeNext), $sUrlNode);
        $sUrlNodeNext = str_replace("{{m}}", date("m", $sTimeNext), $sUrlNode);
        
        
        foreach ($aFrom["url_file"] as $k => $v) {
            $sUrlFile = str_replace("{{Ymd}}", date("Ymd"), $v);
            $sUrlFileNext = str_replace("{{Ymd}}", date("Ymd"), $v);
            
            $sHouzhui = ".".$k;
            if ($k == "v2ray") {
                $sHouzhui = ".txt";
            } else if ($k == "clash") {
                $sHouzhui = ".yaml";
            }
            
            $sFileName = date("Ymd")."-".$k.$sHouzhui;
            
            $sUrlSend = "https://".$sUrlBase.$sUrlNode.$sUrlFile;
            $sUrlSendNext = "https://".$sUrlBase.$sUrlNodeNext.$sUrlFileNext;
            
            $sFileContent = self::curl($sUrlSend) ?? "";
//            $sFileContentNext = self::curl($sUrlSendNext) ?? "";
            
            if (!$sFileContent) {
                return [];
            }
            
            if ($k == "v2ray") {
                $sFileContent = base64_decode($sFileContent);
            }
            
            $sFileContentMd5 = md5($sFileContent);
//            $sFileContentMd5Next = md5($sFileContentNext);
            
//            if ($sFileContentMd5 != $sFileContentMd5Next) {
                $aFileContent[] = [
                    "type"  => $k,
                    "file_name" => $sFileName,
                    "file_content"  => $sFileContent
                ];
//            }
            
        }
        
        return $aFileContent;
    }
    
    private static function fileRealUpd($iId, $sType, $sFileName)
    {
        $oFrom = ModelCrawlFreenode::where("id", $iId)->first();
        
        $aFileReal = json_decode($oFrom->file_real ?? "[]", true);
        
        $aFileReal[$sType] = $sFileName;
        
        $oFrom->file_real = $aFileReal;
        
        return $oFrom->save();
    }
    
    public static function run()
    {
        echo "\nCrawlFreenode::run - 开始\n";
        
        $sDirBase = $_ENV["dir_base"];
        
        $aFromList = ModelCrawlFreenode::where("status", 1)->get()->toArray();
        
        foreach ($aFromList as $aFrom) {
            
            $aFrom["url_file"] = json_decode($aFrom["url_file"], true);
            
            $sMethod = "getFile__".$aFrom["group"];
            $aFileList = self::$sMethod($aFrom); // 没有就返回空数组，所以不用判断，直接foreach

            if (empty($aFileList)) {
                $sMsg = " - CrawlFreenode爬from返回空 -> ".json_encode($aFrom)." \n";
                echo $sMsg;
                TeleSlave::log()->send($sMsg);
            }
            
            foreach ($aFileList as $sFileRow) {
                $sType = $sFileRow["type"];
                
                $sHouzhui = "";
                if ($sType == "v2ray") {
                    $sHouzhui = ".txt";
                } else if ($sType == "clash") {
                    $sHouzhui = ".yaml";
                }
                
                $sFileName = date("Ymd")."-".$sType.$sHouzhui;
                
                $sFileContent = $sFileRow["file_content"];
                
                $sDir = $sDirBase."public/freenode/from/".$aFrom["name"];
//                tomd($sDir, 1);
                $sPath = $sDir."/".$sFileName;
                
                if (!is_dir($sDir)) {
                    mkdir($sDir, 0777, true);
                }
                
//                if ($sType == "v2ray") {
//                    $sFileContent = base64_decode($sFileContent);
//                }

//                file_put_contents($sPath, $sFileContent);
                self::save_utf8_file($sPath, $sFileContent);
                
                self::fileRealUpd($aFrom["id"], $sType, $sFileName);
                
                echo "- ".$aFrom["name"]."::".$sFileName."\n";
            }
            
        }
        
    }
    
    private static function curl($sUrl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回内容而不是直接输出
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果是 https，可以关闭证书验证（测试用）
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 关闭主机名验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 关闭主机名验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);


        $sContent = curl_exec($ch);

        if ($sContent === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            $sMsg = "cURL error ({$errno}): {$error}";
            echo $sMsg;
            TeleSlave::log()->send($sMsg);
        }

        curl_close($ch);
        return $sContent;
    }
    
    private static function fileGet($sUrl)
    {
        $sContent = file_get_contents($sUrl);
        
        return $sContent;
    }
    
    private static function save_utf8_file($filename, $content) {
        // 自动检测编码
        $encoding = mb_detect_encoding(
            $content,
            ['UTF-8', 'GBK', 'BIG5', 'ISO-8859-1'],
            true
        );

        // 如果不是 UTF-8，就转成 UTF-8
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        // 确保文件头是 UTF-8（可选）
         $content = "\xEF\xBB\xBF" . $content; // 如果需要 BOM，可以加上

        
        file_put_contents($filename, $content);
        
        @chmod($filename, 0777);
        
        return true;
    }

    
}
