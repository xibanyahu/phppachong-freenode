<?php

namespace App\Services\Tg\Hook\Mod;

use App\Models\Hook as ModelHook;
//use App\Models\SiteList as ModelSiteList;
//use App\Services\TomTool\Http;

class Hook extends Base {
    
    public function set($aParam, $aData)
    {
        $sKey = $aParam['aArg'][1];
        $iFromId = $aData["message"]["from"]["id"];
        
        ModelHook::where("tg_user_id", $iFromId)->truncate();
        
        $oModelHook = new ModelHook();
        $oModelHook->key = $sKey;
        $oModelHook->tg_user_id = $iFromId;
        $r = $oModelHook->save();
        
        return $this->now($aParam, $aData);
    }
    
    public function un($aParam, $aData)
    {
        $iFromId = $aData["message"]["from"]["id"];
        ModelHook::where("tg_user_id", $iFromId)->truncate();
        
        return $this->now($aParam, $aData);
    }
    
    public function now($aParam, $aData)
    {
        $iFromId = $aData["message"]["from"]["id"];
        $sHookKey = ModelHook::where("tg_user_id", $iFromId)->first()?->key;
        
        if ($sHookKey) {
            $r = $sHookKey;
        } else {
            $r = "hook未挂载";
        }
        
        return $r;
    }
    
}
