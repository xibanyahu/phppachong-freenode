<?php

namespace App\Services\Tg\Hook\Mod;

use App\Models\DoveSite as ModelDoveSite;

class DoveSite extends Base {
    
    public function add($aParam)
    {
        $sCode = $aParam['aArg'][1];
        $oDoveSite = new ModelDoveSite();
        
        $oDoveSite->code = $sCode;
        $r = $oDoveSite->save();
        
        return $r;
    }
    
    public function del($aParam)
    {
        $sCode = $aParam['aArg'][1];
        
        $r = ModelDoveSite::where("code", $sCode)->delete();
        
        return $r;
    }
    
    public function limit($aParam)
    {
        $iLimit = $aParam['aArg'][1];
        
        $oDoveSite = ModelDoveSite::orderBy("code")->limit($iLimit)->get();
        
        $this->eachSendTg($oDoveSite);
    }
    
    public function like($aParam)
    {
        $sField = $aParam['aOption'][1];
        $sValue = $aParam['aArg'][1];
        
        $oDoveSiteLike = ModelDoveSite::where($sField, "like", "%".$sValue."%")->get();
        
        $this->eachSendTg($oDoveSiteLike);
        
    }
    
    public function Set($aParam)
    {
        $sCode = $aParam['aOption'][1];
        $sField = $aParam['aOption'][2];
        $sValue = $aParam['aArg'][1];
        
        $oDoveSite = ModelDoveSite::where("code", $sCode)->first();
        
        if (!$oDoveSite) {
            throw new \Exception("没有这个code");
        }
        
        $oDoveSite->$sField = $sValue;
        $r = $oDoveSite->save();
        
        return $r;
    }
    
    
    
}
