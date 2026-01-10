<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\Tg\Hook\Dog;
use App\Models\HttpQueue as ModelHttpQueue;

class HttpQueue extends Base {
    
    private $oDog;
//    private $aUpdate;

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelHttpQueue());
        $this->oDog->_setDogName("HttpQueue");
//        $this->oDog->_setPrimaryKey("code"); // 可选，默认id
//        $this->oDog->_setJsonField(["config"]);
//        $this->oDog->_setStrField(["content"]);
//        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
    
    
}
