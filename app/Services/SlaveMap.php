<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SiteMap as ModelSiteMap;
use App\Models\SitePathMap as ModelSitePathMap;
use App\Models\ArticleSiteMapGroup as ModelArticleSiteMapGroup;
use App\Services\TomTool\Flow;

final class SlaveMap
{
    
    public static function siteByGroup(string $sSiteGroupCode): array
    {
        $aSiteList = ModelSiteMap::where("group_code", $sSiteGroupCode)->pluck("code")->toArray();
        
        return $aSiteList;
    }
    
    public static function siteGroupByArticleGroup(string $sArticleGoupCode)
    {
        $aSiteGroupList = ModelArticleSiteMapGroup::where("article_group_code", $sArticleGoupCode)->pluck("site_group_code")->toArray();
        
        return $aSiteGroupList;
    }
    
//    public static function siteByArticleGroup(string $sArticleGoupCode)
//    {
//        $aSiteGroupList = self::siteGroupByArticleGroup($sArticleGoupCode);
//        
//        $aSiteList = ModelSiteMap::whereIn("group_code", $aSiteGroupList)->orderBy("group_code")->get()->toArray();
//        
//        return $aSiteList;
//    }
    
        public static function cmdListByArticleGroup(string $sArticleGoupCode): array
        {
            $aSiteGroupList = self::siteGroupByArticleGroup($sArticleGoupCode);
    
            $aCmdList = [];
            foreach ($aSiteGroupList as $sSiteGroupCode) {
    
                $aSiteList = self::siteByGroup($sSiteGroupCode);
    
                foreach ($aSiteList as $sSiteCode) {
                    $sCmdKey = "|{$sSiteGroupCode}|{$sSiteCode}|";
                    $aCmdList[] = $sCmdKey;
                }
    
            }
    
            return $aCmdList;
        }
    
//    public static function cmdTreeByArticleGroup(string $sArticleGoupCode, $xDefaultCmd = null): array
//    {
//        $aSiteGroupList = self::siteGroupByArticleGroup($sArticleGoupCode);
//        
//        $aTree = [];
//        foreach ($aSiteGroupList as $sSiteGroupCode) {
//            
//            $aSiteList = self::siteByGroup($sSiteGroupCode);
//            
//            foreach ($aSiteList as $sSiteCode) {
//                $aTree[$sSiteGroupCode][$sSiteCode] = $xDefaultCmd;
//            }
//            
//        }
//        
//        return $aTree;
//    }
    
    public static function sitePathByGroup(string $sSiteGroupCode, string $sPathGroupCode): Flow
    {
        $oFlow = Flow::start("sitePathByGroup");
        
        $oSiteList = ModelSiteMap::where("group_code", $sSiteGroupCode)->get();
        
        if ($oSiteList->isEmpty()) {
            return $oFlow->fail("no have site");
        }
        
        $sPath = ModelSitePathMap::where("site_group_code", $sSiteGroupCode)->where("path_group_code", $sPathGroupCode)->first()?->path;
        
        if (!$sPath) {
            return $oFlow->fail("no have path");
        }
        
        $aSitePath = [];
        foreach ($oSiteList as $oSite) {
            $aSitePath[] = $oSite->url.$sPath;
        }
        
        return $oFlow->aData($aSitePath)->done();
    }
    
    public static function sitePathByCode(string $sSiteCode, string $sPathGroupCode): Flow
    {
        $oFlow = Flow::start("sitePathByCode");
        
        $oSite = ModelSiteMap::where("code", $sSiteCode)->first();
        
        if (!$oSite) {
            return $oFlow->fail("no have site");
        }
        
        $sPath = ModelSitePathMap::where("site_group_code", $oSite->group_code)->where("path_group_code", $sPathGroupCode)->first()?->path;
        
        if (!$sPath) {
            return $oFlow->fail("no have path");
        }
        
        $sSitePath = $oSite->url.$sPath;
        
        return $oFlow->sData($sSitePath)->done();
    }
    
}
