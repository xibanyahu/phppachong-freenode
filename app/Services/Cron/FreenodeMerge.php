<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Models\CrawlFreenode as ModelCrawlFreenode;
use App\Models\FreenodePool as ModelFreenodePool;
use App\Services\Cron\FreenodePool as CronFreenodePool;
use App\Models\SiteMap as ModelSiteMap;
use App\Models\Option as ModelOption;
use App\Services\TomTool\Telegram\Slave as TeleSlave;
use Symfony\Component\Yaml\Yaml;

final class FreenodeMerge
{
    private static $aCountryOrder = [
        "{{slogan_siteUrl}}",
        "{{slogan_telegram}}",
        "{{slogan_date}}",
        "美国",
        "日本",
        "台湾",
        "香港",
        "新加坡",
    ];
    
    private static $aCountryFlag = [
        "美国"    => "🇺🇸",
        "日本"    => "🇯🇵",
        "台湾"    => "🇹🇼",
        "香港"    => "🇭🇰",
        "新加坡"  => "🇸🇬",
        "越南"    => "🇻🇳",
        "俄罗斯"   => "🇷🇺",
        "韩国"    => "🇰🇷",
    ];
    
//    public static function buildSogan($aNode)
//    {
//        // todo 广告位，这样就固定了
//    }
    
    private static function isSlogan($s)
    {
        if ($s == "{{slogan_siteUrl}}" || $s == "{{slogan_telegram}}" || $s == "{{slogan_date}}") {
            return true;
        }
        
        return false;
    }
    
    public static function save()
    {
        echo "\nFreenodeFile::save - 开始 \n";
        
        //// test
//        $a = ["a", "b", "c"];
//        $b = ["b", "c", "d"];
//        $c = array_merge($a, $b);
//        $c = array_unique($c);
//        $c = array_values($c);
//        tomd($c, 1);
        //// test end
        
        $sDirBase = $_ENV["dir_base"];
        
        $sDate = date("Y-m-d");
//        $sDate = "2025-12-12"; // *test
        
        $oPool = ModelFreenodePool::whereDate("date", $sDate)->first();
        
        $aPoolContentList = json_decode($oPool->content, true);
        
        foreach ($aPoolContentList as $sPoolClient => $aPoolContent) {
            
            $aNodeGroupCountry = [];
            $aLastNode = [];
            $aLastNodeMy = [];
            foreach ($aPoolContent as $sMd5 => $aNode) {
                $sFromName = $aNode["fromName"];
                $sCountry = $aNode["country"];
                $sProtocolType = $aNode["protocolType"];
                $sConfig = $aNode["config"];
                
                $sCountryClear = self::strReplace($sCountry, $sFromName);
                
                $aNodeGroupCountry[$sCountryClear][] = $aNode;
                
                if ($sFromName == "_my") {
                    $aLastNodeMy = $aNode;
                }
                
                $aLastNode = $aNode;
            }
            
            $aSloganNode = $aLastNode;
            if ($aLastNodeMy) {
                $aSloganNode = $aLastNodeMy;
            }
            
            $aNodeGroupCountry["{{slogan_siteUrl}}"][] = $aSloganNode;
            $aNodeGroupCountry["{{slogan_telegram}}"][] = $aSloganNode;
            $aNodeGroupCountry["{{slogan_date}}"][] = $aSloganNode;
            
            $aNodeSortCountry = [];
            
            foreach (self::$aCountryOrder as $sCountryOrder) {
                if (array_key_exists($sCountryOrder, $aNodeGroupCountry)) {
                    if (self::isSlogan($sCountryOrder)) {
                        $aNodeSortCountry[$sCountryOrder] = [
                            $aNodeGroupCountry[$sCountryOrder][0] // 得到多个slogan只要第一个，按理不用处理，保险
                        ];
                    } else {
                        $aNodeSortCountry[$sCountryOrder] = $aNodeGroupCountry[$sCountryOrder];
                    }
                }
            }

            foreach ($aNodeGroupCountry as $sCountry => $aNode) {
                if (!array_key_exists($sCountry, self::$aCountryOrder)) {
                    $aNodeSortCountry[$sCountry] = $aNode;
                }
            }

            $aNodeMerge = [];
            $aNodeCountryList = [];
            foreach ($aNodeSortCountry as $sCountry => $aNodeList) {
                $i = 1;
                foreach ($aNodeList as $aNode) {
                    if (self::isSlogan($sCountry)) {
                        $aNode["country"] = $sCountry;
                    } else {
                        $aNode["country"] = $sCountry." ".sprintf('%02d', $i);
                    }
                    
                    $aNode["country"] = $aNode["country"]." :[".$aNode["fromName"]."]:";
                    
                    if ($sPoolClient == "v2ray") {
                        $sConfig = $aNode["config"];
                        if (is_array($sConfig)) {
                            $sConfig = json_encode($sConfig);
                        }
                        $sNode = $aNode["protocolType"]."://".$sConfig."#".urlencode($aNode["country"]);
                        $aNodeMerge[] = $sNode;
                    } else if ($sPoolClient == "clash") {
                        $sFlagCountry = self::countryJoinFlag($aNode["country"]);
                        $aNode["config"]["name"] = $sFlagCountry;
                        $aNodeMerge[] = $aNode["config"];
                        $aNodeCountryList[] = $sFlagCountry;
                    } else {
                        $aNodeMerge[] = json_encode($aNode);
                    }
                    $i++;
                }
            }

            if ($sPoolClient == "v2ray") {
                
                $sMerge = implode("\n", $aNodeMerge);
                $sMerge = base64_encode($sMerge);
                
            } else if ($sPoolClient == "clash") {

                $sFile = $sDirBase."public/freenode/template/clash.txt";
                
                if (file_exists($sFile) && is_readable($sFile)) {
                    $sClashTemplate = file_get_contents($sFile); // data
                } else {
                    $sMsg = "clash的template不存在，这不应该 \n";
                    TeleSlave::warn()->send($sMsg);
                    echo $sMsg;
                    continue;
                }
                
                $aClashTemplate = Yaml::parse($sClashTemplate);
                
                $aClashTemplate["proxies"] = $aNodeMerge;
                
                $aProxyGroupList = &$aClashTemplate["proxy-groups"];
                
                foreach ($aProxyGroupList as &$aProxyGroup) {
                    
                    if (!isset($aProxyGroup["proxies"])) {
                        $aProxyGroup["proxies"] = [];
                    }
                    
                    if ($aProxyGroup["name"] == "🍃 应用净化") {
                        continue;
                    }
                    
                    if ($aProxyGroup["name"] == "🛑 全球拦截") {
                        continue;
                    }
                    
                    if ($aProxyGroup["name"] == "🎯 全球直连") {
                        continue;
                    }
                    
                    $aProxyGroup["proxies"] = self::arrMerge($aProxyGroup["proxies"], $aNodeCountryList);
                }
                
                $sMerge = Yaml::dump($aClashTemplate, 4);
            }
            
            $sFileDir = $sDirBase."public/freenode/merge/base/".$sPoolClient;
            if (!is_dir($sFileDir)) {
                mkdir($sFileDir, 0777, true);
            }
            
            $sFilePath = $sFileDir."/".$sDate.".txt"; // 都是txt，解析的时候另处理
            file_put_contents($sFilePath, $sMerge);
            
            @chmod($sFilePath, 0777);
            
            echo " - base::".$sFilePath." 已保存 \n";
        }
        
    }
    
    public static function baseToSite()
    {
        echo "\nFreenodeMerge::baseToSite - 开始 \n";
        
        $sDate = date("Y-m-d");
//        $sDateCode = date("Ymd");
//        $sDate = "2025-12-12"; // *test
        
        $sDirBase = $_ENV["dir_base"];
        
        $sFnClientList = ModelOption::_hit("freenode_client-fn_a");
        
        if (!$sFnClientList) {
            throw new \Exception("未设置option的freenode_client？");
        }
        
        $aFnClientList = explode(",", $sFnClientList);
        
        $aMerge = [];
        foreach ($aFnClientList as $sFnClient) {
            $sMergeFilePath = $sDirBase."public/freenode/merge/base/".$sFnClient."/".$sDate.".txt";
            
            if (file_exists($sMergeFilePath)) {
                $aMerge[$sFnClient] = file_get_contents($sMergeFilePath);
            } else {
                throw new \Exception("baseToSite里没找到merge文件？ - $sMergeFilePath");
            }
        }
        
        $oSiteMap = ModelSiteMap::where("group_code", "fn_a")->get();
        
        if ($oSiteMap->isEmpty()) {
            throw new \Exception("没找到siteMap的fn_a组？");
        }
        
        $aSiteMap = $oSiteMap->toArray();
        $aSiteMap[] = [
            "code"  => "_admin",
            "url"   => "_admin.loc",
            "name"  => "_admin",
        ];
        
        $iH4Rand = rand(1, 21600); // 6小时
        $iH4Time = time() - $iH4Rand;
        
        $sSloganDate = date("Y-m-d H:i:s", $iH4Time);

        foreach ($aSiteMap as $aSiteMapRow) {
            $aSiteConfig = json_decode($aSiteMapRow["config"] ?? "", true);
            
            $sSiteConfigTelegram = $aSiteConfig["telegram"] ?? "";
            
            $sSiteConfigSloganUrl = $aSiteConfig["freenode_slogan"]["siteUrl"] ?? "";
            $sSiteConfigSloganTelegram = $aSiteConfig["freenode_slogan"]["telegram"] ?? "";

            if ($sSiteConfigSloganUrl) {
                $sSlogan_siteUrl = str_replace('{{siteUrl}}', $aSiteMapRow["url"], $sSiteConfigSloganUrl);
            } else {
                $sSlogan_siteUrl = "美国".uniqid();
            }
            
            if ($sSiteConfigSloganTelegram) {
                $sSlogan_telegram = str_replace('{{telegram}}', '@'.$sSiteConfigTelegram, $sSiteConfigSloganTelegram);
            } else {
                $sSlogan_telegram = "美国".uniqid();
            }
            
            foreach ($aMerge as $sClient => $sFeedContent) {
                $sFileSaveDir = $sDirBase."public/freenode/merge/".$aSiteMapRow["code"]."/".$sClient;
                
                if (!is_dir($sFileSaveDir)) {
                    mkdir($sFileSaveDir, 0777, true);
                }
                
                $sFileSavePath = $sFileSaveDir."/".$sDate.".txt";
                
                if ($sClient == "v2ray") {
                    $sFeedContent = self::v2rayFeedToStr($sFeedContent);
                }

                $sFeedContent = str_replace('{{slogan_siteUrl}}', $sSlogan_siteUrl, $sFeedContent);
                $sFeedContent = str_replace('{{slogan_telegram}}', $sSlogan_telegram, $sFeedContent);
                $sFeedContent = str_replace('{{slogan_date}}', "更新于：".$sSloganDate, $sFeedContent);
                
                if ($aSiteMapRow["code"] !== "_admin") {
                    $sFeedContent = preg_replace('/ :\[.*?\]:/su', '', $sFeedContent);
                }
                
                if ($sClient == "v2ray") {
                    $sFeedContent = self::v2rayFeedByStr($sFeedContent);
                }
                
                file_put_contents($sFileSavePath, $sFeedContent);
                
                @chmod($sFileSavePath, 0777);
                
                echo " - fn的".$aSiteMapRow["code"]."的{$sClient}的{$sDate} 保存成功 \n";
            }
        }
        
        return true;
    }
    
    public static function v2rayFeedByStr($s)
    {
        $a = explode("\n", $s);
        
        $aFeed = [];
        foreach ($a as $sConfig) {
            if (!$sConfig) {continue;}
            $aTmp = CronFreenodePool::v2ray_chai($sConfig);
            $sConfig = $aTmp[0];
            $sName = $aTmp[1];
            $sName = rawurlencode($sName);
            $aFeed[] = $sConfig."#".$sName;
        }
        
        $sFeed = implode("\n", $aFeed);
        $sFeed = base64_encode($sFeed);
        
        return $sFeed;
    }
    
    public static function v2rayFeedToStr($sV2rayFeed)
    {
        $sV2rayFeed = base64_decode($sV2rayFeed);
        $aV2rayFeed = explode("\n", $sV2rayFeed);

        $aFeed = [];
        foreach ($aV2rayFeed as $sConfig) {
            if (!$sConfig) {continue;}
            $aTmp = CronFreenodePool::v2ray_chai($sConfig);
            $sConfig = $aTmp[0];
            $sName = $aTmp[1] ?? "美国"; // 没得到名字默认美国
            $sName = urldecode($sName);
            
            $aFeed[] = $sConfig."#".$sName;
        }

        $sFeed = implode("\n", $aFeed);

        return $sFeed;
    }
    
    private static function arrMerge($aBase, $aNew)
    {
        $aMerge = array_merge($aBase, $aNew);
        $aMerge = array_unique($aMerge);
        $aMerge = array_values($aMerge);
        return $aMerge;
    }
    
    public static function strReplace($sStr, $sFromName)
    {
        if (preg_match('/机场/u', $sStr)) {
            tomd($sStr);
            $sStr = "美国"; // 原本的slogan直接视为美国
        }
        
        if (preg_match('/频道/u', $sStr)) {
            $sStr = "美国";
        }

        if ($sFromName == "ripaojiedian") {
            $sStr = preg_replace('/\|\@ripaojiedian/', '', $sStr);
        } else if ($sFromName == "stairnode") {
            $sStr = preg_replace('/\|\@stairnode/', '', $sStr);
        } else {
            // 其他
        }
        
//        $sStr = preg_replace('/\d+/', '', $sStr); // 去数字
//        $sStr = preg_replace('/[\x{1F1E6}-\x{1F1FF}]{2}\s*/u', '', $sStr); // 去国旗
//        $sStr = preg_replace('/[a-zA-Z_]+/', '', $sStr); // 去下划线和字母

        if (preg_match('/\p{Han}/u', $sStr)) {
            $sStr = preg_replace('/[^\p{Han}]/u', '', $sStr);// 只留中文
        } else { // 没有中文，再处理一下
            $sStr = preg_replace('/^.*HK.*$/i', '香港', $sStr);
        }
        
        return $sStr;
    }
    
    private static function countryJoinFlag($str)
    {
        $aCountryFlagList = self::$aCountryFlag;
        
        $sFlag = "";
        foreach ($aCountryFlagList as $sCountryName => $sCountryFlag) {
            if (str_contains($str, $sCountryName)) {
                $sFlag = $sCountryFlag;
            }
        }
        
        if ($sFlag) {
            $str = $sFlag." ".$str;
        }
        
        return $str;
    }
    
}
