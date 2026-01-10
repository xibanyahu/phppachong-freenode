<?php

namespace App\Services\Tg\Hook\Mod;

use App\Models\SitePathMap as ModelSitePathMap;
use App\Services\Tg\Hook\Dog;

class SitePathMap extends Base {
    
    private $oDog;
//    private $aUpdate;

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelSitePathMap());
        $this->oDog->_setDogName("sitePathMap");
//        $this->oDog->_setPrimaryKey("code"); // 可选，默认id
//        $this->oDog->_setJsonField(["use_from"]);
//        $this->oDog->_setStrField(["content"]);
//        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
    
    
}
