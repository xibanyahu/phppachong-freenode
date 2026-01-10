<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Models\CrawlFreenode as ModelCrawlFreenode;
use App\Models\FreenodePool as ModelFreenodePool;
use App\Services\TomTool\Telegram\Slave as TeleSlave;
use Symfony\Component\Yaml\Yaml;

final class FreenodePool
{
    
    public static function save()
    {
        echo "FreenodePoll::save - 开始 \n";
        $sDirBase = $_ENV["dir_base"];
        $sDate = date("Y-m-d");
//        $sDate = "2025-12-12"; // *test
        
        $oPool = ModelFreenodePool::whereDate("date", $sDate)->first();
        
        if (!$oPool) {
            $oPool = new ModelFreenodePool();
            $oPool->date = $sDate;
            $oPool->save();
        }
        
        $oFromList = ModelCrawlFreenode::where("status", 1)->get();
        
        $aMerge = [];
        foreach ($oFromList as $oFrom) {
            $aFromFileReal = json_decode($oFrom->file_real, true);
            foreach ($aFromFileReal as $sFromFileRealClient => $aFromFileName) {
                
                $sFromFilePath = $sDirBase."public/freenode/from/".$oFrom->name."/".$aFromFileName;

                if (file_exists($sFromFilePath) && is_readable($sFromFilePath)) {
                    $sFromFileContent = file_get_contents($sFromFilePath); // data
                } else {
                    $sMsg = "file_real不存在，这不应该 -> ".$sFromFilePath." \n";
                    TeleSlave::warn()->send($sMsg);
                    echo $sMsg;
                    continue;
                }
                
                $aNodeFormat = [
                    "fromName"      => $oFrom->name,
                    "country"       => "",
                    "protocolType"  => "",
                    "config"        => "",
                ];
                
                if ($sFromFileRealClient == "v2ray") {
                    $aFromFileContent = explode("\n", $sFromFileContent);
                    foreach ($aFromFileContent as $sNode) {
                        if (!$sNode) {
                            continue;
                        }

                        $aTmp = explode("://", $sNode, 2);
                        $aNodeFormat["protocolType"] = $sProtocolType = $aTmp[0]; // data
                        $sConfig = $aTmp[1];
                        
                        if ($sProtocolType == "vmess") {
                            $sConfig = base64_decode($sConfig);
                            $aConfig = json_decode($sConfig, true);
                            $aNodeFormat["country"] = $sCountry = $aConfig["ps"]; // data
                            $aConfig["ps"] = "";
                            $aNodeFormat["config"] = $aConfig; // data
                            $sConfigMd5 = md5(json_encode($aConfig));
                        } else if ($sProtocolType == "ss") {
                            $sConfig = urldecode($sConfig);
                            $aTmp = self::v2ray_chai($sConfig);
                            $aNodeFormat["config"] = $sConfig = $aTmp[0]; // data
                            $aNodeFormat["country"] = $sCountry = $aTmp[1]; // data
//                            if ($oFrom->group == "A") { // ripaojiedian系ss协议bug修复
//                                $aTmp = explode("@", $sConfig);
//                                $qian = $aTmp[0];
//                                $hou = $aTmp[1];
//                                $qian = base64_decode($qian);
//                                $he = $qian."@".$hou;
//                                $he = base64_encode($he);
//                                $sConfig = $he;
//                                $aNodeFormat["config"] = $sConfig;
//                            }

                            $sConfigMd5 = md5($sConfig);
                        } else {
                            $sConfig = urldecode($sConfig);
                            $aTmp = self::v2ray_chai($sConfig);
                            $aNodeFormat["config"] = $sConfig = $aTmp[0]; // data
                            $aNodeFormat["country"] = $sCountry = $aTmp[1] ?? '美国'; // data
                            $sConfigMd5 = md5($sConfig);
                        }
                        
                        $aMerge[$sFromFileRealClient][$sConfigMd5] = $aNodeFormat;
                    }
                } else if ($sFromFileRealClient == "clash") {

                    $bom = pack('H*','EFBBBF');
                    $sFromFileContent = preg_replace("/^$bom/", '', $sFromFileContent);
                    $aFromFileContent = Yaml::parse($sFromFileContent);
                    $aFromFileContent = $aFromFileContent["proxies"];

                    foreach ($aFromFileContent as $aNode) {
                        $aNodeFormat["protocolType"] = $aNode["type"];
                        $aNodeFormat["country"] = $aNode["name"]; // data
                        $aNode["name"] = "";
                        $aNodeFormat["config"] = $aNode; // data
                        $sConfigMd5 = md5(json_encode($aNode));
                        $aMerge[$sFromFileRealClient][$sConfigMd5] = $aNodeFormat;
                    }
                    
                }
            }
        }

        $oPool->content = $aMerge;
        $oPool->save();
        
        echo " - freenode_pool更新完成 \n";
    }
    
//    public static function contentFormat($aContentFrom, $sClientType)
//    {
//        $aDataMerge = [];
//        foreach ($aContentFrom as $sFromName => $aContent) {
//            foreach ($aContent as $mContent) {
//                $aNodeData = [];
//                $sProtocolType = "";
//                $mConfig = "";
//                $sCountry = "";
//                $sConfig = "";
//                
//                if ($sClientType == "v2ray") {
//                    $aTmp = self::v2ray_chai($mContent);
//                    $sTmp = $aTmp[0] ?? "none";
//                    $sCountry = $aTmp[1] ?? "none"; // common
//                    $aTmp = explode("://", $sTmp, 2);
//                    $sProtocolType = $aTmp[0] ?? "none"; // common
//                    $mConfig = $aTmp[1] ?? "none"; // common
//                    $sConfig = $mConfig; // common
//                } else if ($sClientType == "clash") {
//                    $sCountry = $mContent["name"]; // common
//                    $sProtocolType = $mContent["type"]; // common
//                    $mConfig = $mContent; // common
//                    $mConfig["name"] = "";
//                    $sConfig = json_encode($mContent); // common
//                }
//                
//                $aNodeData = [
//                    "from"  => $sFromName,
//                    "clientType"    => $sClientType,
//                    "country"       => $sCountry,
//                    "protocolType"  => $sProtocolType,
//                    "config"    => $mConfig,
//                ];
//                
//                $sConfigMd5 = md5($sConfig);
//                
//                $aDataMerge[$sConfigMd5] = [$aNodeData];
//                
////                tomd($mConfig);
//                
////                tomd($sContent);
//            }
//        }
//        
//        tomd($aDataMerge, 1);
//    }
    
    public static function v2ray_chai($sFromContentRow)
    {
        $pos = strrpos($sFromContentRow, "#");
        if ($pos !== false) {
            $aTmp = [
                substr($sFromContentRow, 0, $pos),
                substr($sFromContentRow, $pos + 1)
            ];
        } else {
            $aTmp = [$sFromContentRow]; // 没有分隔符时，返回原字符串
        }

        return $aTmp;
    }
    
//    public static function save_old()
//    {
//        self::save_custom();
//        self::save_v2ray();
//        self::save_clash();
//    }
    
//    public static function save_clash()
//    {
//        $sDate = date("Y-m-d");
//        
//        $oPool = ModelFreenodePool::whereDate("date", $sDate)->first();
//        
//        if (!$oPool) {
//            $oPool = new ModelFreenodePool();
//            $oPool->date = $sDate;
//            $oPool->save();
//        }
//        
//        if (!$oPool->content_custom) {
//            $oPool->content_clash = "[]";
//            $oPool->save();
//        }
//
//        $aPoolContent = json_decode($oPool->content_clash, true);
//        
//        $oFromList = ModelCrawlFreenode::where("status", 1)->get();
//        
//        foreach ($oFromList as $oFrom) {
//            $sFromName = $oFrom->name;
//            $aFromFileReal = json_decode($oFrom->file_real, true);
//            $sFromFileClash = $aFromFileReal["clash"];
//            
//            $sFromFilePath = "freenode/from/".$sFromName."/".$sFromFileClash;
//            
//            if (file_exists($sFromFilePath) && is_readable($sFromFilePath)) {
//                $sFromFileContent = file_get_contents($sFromFilePath);
//                
//            } else {
//                $sMsg = "file_real不存在，这不应该 -> ".json_encode($oFrom->toArray());
//                TeleSlave::warn()->send($sMsg);
//                echo $sMsg;
//                continue;
//            }
//            
//            $aFromFileContent = Yaml::parse($sFromFileContent);
//            
//            $aFn = $aFromFileContent["proxies"];
//            
//            $aPoolContent[$sFromName] = $aFn;
//            
//            $oPool->content_clash = $aPoolContent;
//            $oPool->save();
//            
//            echo " - {$sFromName}的clash更新完成 \n";
//        }
//        
//    }
    
//    public static function save_v2ray()
//    {
//        $sDate = date("Y-m-d");
//        
//        $oPool = ModelFreenodePool::whereDate("date", $sDate)->first();
//        
//        if (!$oPool) {
//            $oPool = new ModelFreenodePool();
//            $oPool->date = $sDate;
//            $oPool->save();
//        }
//        
//        if (!$oPool->content_custom) {
//            $oPool->content_v2ray = "[]";
//            $oPool->save();
//        }
//
//        $aPoolContent = json_decode($oPool->content_v2ray, true);
//        
//        $oFromList = ModelCrawlFreenode::where("status", 1)->get();
//        
//        foreach ($oFromList as $oFrom) {
//            $sFromName = $oFrom->name;
//            $aFromFileReal = json_decode($oFrom->file_real, true);
//            $sFromFileV2ray = $aFromFileReal["v2ray"];
//            
//            $sFromFilePath = "freenode/from/".$sFromName."/".$sFromFileV2ray;
//            
//            if (file_exists($sFromFilePath) && is_readable($sFromFilePath)) {
//                $sFromFileContent = file_get_contents($sFromFilePath);
//                
//            } else {
//                $sMsg = "file_real不存在，这不应该 -> ".json_encode($oFrom->toArray());
//                TeleSlave::warn()->send($sMsg);
//                echo $sMsg;
//                continue;
//            }
//            
//            $aFromFileContent = explode("\n", $sFromFileContent);
//            
//            foreach ($aFromFileContent as &$sFromFileContentRow) {
//                if (!$sFromFileContentRow) {
//                    continue;
//                }
//
////                $aTmp = explode("://", $sFromFileContentRow, 2);
////                $sVtype = $aTmp[0];
////                $sVconfig = $aTmp[1];
////                if ($sVtype == "vmess") {
////                    $s = base64_decode($sVconfig);
////                    tomd($s, 1);
////                }
//                
//                $sFromFileContentRow = urldecode($sFromFileContentRow);
//            }
//            
//            $aPoolContent[$sFromName] = $aFromFileContent;
//            
//            $oPool->content_v2ray = $aPoolContent;
//            $oPool->save();
//            
//            echo " - {$sFromName}的v2ray更新完成 \n";
//        }
//        
//    }
    
    public static function save_custom()
    {
        $sDate = date("Y-m-d");
        
        $oPool = ModelFreenodePool::whereDate("date", $sDate)->first();
        
        if (!$oPool) {
            $oPool = new ModelFreenodePool();
            $oPool->date = $sDate;
            $oPool->save();
        }
        
        if (!$oPool->content_custom) {
            $oPool->content_custom = "[]";
            $oPool->save();
        }
        
        $aPoolContent = json_decode($oPool->content_custom, true);
        
        $oFromList = ModelCrawlFreenode::where("status", 1)->get();
        
        foreach ($oFromList as $oFrom) {
            $sFromName = $oFrom->name;
            $aFromFileReal = json_decode($oFrom->file_real, true);
            $sFromFileV2ray = $aFromFileReal["v2ray"];
            
            $sFromFilePath = "freenode/from/".$sFromName."/".$sFromFileV2ray;
            
            if (file_exists($sFromFilePath) && is_readable($sFromFilePath)) {
                $sFromFileContent = file_get_contents($sFromFilePath);
            } else {
                $sMsg = "file_real不存在，这不应该 -> ".json_encode($oFrom->toArray());
                TeleSlave::warn()->send($sMsg);
                echo $sMsg;
            }
            
            $aFromFileContent = explode("\n", $sFromFileContent);

            
            $i = 0;
            $aFnNew = [];
            foreach ($aFromFileContent as $sFromFileContentRow) {
                if (!$sFromFileContentRow) {
                    continue;
                }

                $ss = explode("://", $sFromFileContentRow, 2);

                list($sProtocolType, $sFn) = explode("://", $sFromFileContentRow, 2);

                $sMethod = $sProtocolType . "ToArr";

                if (method_exists(__CLASS__, $sMethod)) {
                    $aFn = self::$sMethod($sFn);
                } else {
                    TeleSlave::warn()->send("未设处理方式的协议 -> ".$sFromFileContentRow);
                    continue;
                }

                $aFnNew[] = $aFn;
            }
            
            $aPoolContent[$sFromName] = $aFnNew;
            $oPool->content_custom = $aPoolContent;
            $oPool->save();

            echo " - {$sFromName}的custom更新完成 \n";
        }

    }
    
    private static function vmessToArr($sStr)
    {
        $sStr = base64_decode($sStr);
        $a = json_decode($sStr, true);
        $a["vtype"] = "vmess";
        
        return $a;
    }
    
    private static function ssToArr($sStr)
    {
// YWVzLTI1Ni1jZmI6YW1hem9uc2tyMDU@63.180.254.10:443#8%E5%85%83%E8%80%81%E7%89%8C%E4%B8%93%E7%BA%BF%E6%9C%BA%E5%9C%BA%EF%BC%9Acczzuu.top
        $sStr = urldecode($sStr);
        list($sPwd, $sStr) = explode("@", $sStr, 2);
        $sPwd = base64_decode($sPwd);
        list($sCipher, $sPwd) = explode(":", $sPwd, 2);
        list($sServer, $sStr) = explode("#", $sStr, 2);
        list($sServer, $sPort) = explode(":", $sServer, 2);
        $aPort = explode("/?", $sPort, 2);
        $sPort = $aPort[0];
        $sOption = $aPort[1] ?? "";

        if ($sOption) {
            $aOptionList = explode(";", $sOption);
        } else {
            $aOptionList = [];
        }
        
        $aOptionArr = [];
        foreach ($aOptionList as $sOption) {
            if (!$sOption) {
                continue;
            }
            $aTmp = explode("=", $sOption, 2);
            $k = $aTmp[0] ?? "";
            $v = $aTmp[1] ?? "";
            $aOptionArr[$k] = $v;
        }
        
        $sName = $sStr;
        
        $a = [
            "vtype"      => "ss",
            "server"    => $sServer,
            "port"      => $sPort,
            "cipher"    => $sCipher,
            "password"       => $sPwd,
            "name"      => $sName,
            "option"    => $aOptionArr,
        ];
        
        return $a;
        
    }
    
    private static function trojanToArr($sStr)
    {
// 76d630f2af6619c4a5de0ef953df3c6a@58.152.110.154:443?allowInsecure=0&sni=www.nintendogames.net#%F0%9F%87%AD%F0%9F%87%B0%20%E9%A6%99%E6%B8%AF3%7C%40stairnode
        $sStr = urldecode($sStr);
        $aTmp = explode("@", $sStr, 2);
        $sPwd = $aTmp[0] ?? "";
        $sStr = $aTmp[1] ?? "";
        
        $aTmp = explode("#", $sStr, 2);
        $sStr = $aTmp[0] ?? "";
        $sName = $aTmp[1] ?? "";
        
        $aTmp = explode("?", $sStr, 2);
        $sServer = $aTmp[0] ?? "";
        $sStr = $aTmp[1] ?? "";
        
        $aTmp = explode(":", $sServer, 2);
        $sServer = $aTmp[0] ?? "";
        $sPort = $aTmp[1] ?? "";
        
        $aOptionList = explode("&", $sStr, 2);
        
        $aOptionArr = [];
        foreach ($aOptionList as $sOption) {
            if (!$sOption) {
                continue;
            }
            $aTmp = explode("=", $sOption, 2);
            $k = $aTmp[0] ?? "";
            $v = $aTmp[1] ?? "";
            $aOptionArr[$k] = $v;
        }

        $a = [
            "vtype"      => "trojan",
            "name"      => $sName,
            "server"    => $sServer,
            "port"      => $sPort,
            "password"  => $sPwd,
            "option"    => $aOptionArr,
        ];
        
        return $a;
    }
    
//    public static function show($sDate = "")
//    {
//        if (!$sDate) {
//            $sDate = date("Y-m-d");
//        }
//        
//        $oPool = ModelFreenodePool::whereDate("date", $sDate)->first();
//        
//        if (!$oPool) {
//            return "没有？";
//        }
//        
//        return $oPool->content_custom;
//    }
    
    
}
