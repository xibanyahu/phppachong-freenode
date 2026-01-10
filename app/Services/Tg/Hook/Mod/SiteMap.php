<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\Tg\Hook\Dog;
use App\Models\SiteMap as ModelSiteMap;

class SiteMap extends Base {
    
    private $oDog;
//    private $aUpdate;

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelSiteMap());
        $this->oDog->_setDogName("siteMap");
        $this->oDog->_setPrimaryKey("code"); // 可选，默认id
        $this->oDog->_setJsonField(["config", "api_path"]);
//        $this->oDog->_setStrField(["content"]);
//        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
    
    
}
