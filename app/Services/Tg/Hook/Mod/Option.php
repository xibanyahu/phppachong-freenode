<?php

namespace App\Services\Tg\Hook\Mod;
use App\Models\Option as ModelOption;
use App\Services\Tg\Hook\Dog;

class Option extends Base {
    
    private $oDog;

    public function __construct($aUpdate = [])
    {
        $this->oDog = new Dog(new ModelOption());
        $this->oDog->_setPrimaryKey("k"); // 可选，默认id
        $this->oDog->_setDogName("option"); // 必须
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
//    public function Pause($aParam)
//    {
//        $v = $aParam['aArg'][1];
//        
//        // 获取第一个匹配的选项
//        $oOption = ModelOption::where("type", "pause")->first();
//        
//        // 检查是否找到了选项
//        if ($oOption) {
//            $oOption->value = $v;
//            
//            // 保存并返回结果
//            return json_encode($oOption->save());
//        }
//        
//        // 如果没有找到选项，返回一个错误信息
//        return json_encode(['error' => 'Option not found']);
//    }
//    
//    public function List()
//    {
//        $aOptionList = ModelOption::all()->toArray();
//        
//        return json_encode($aOptionList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//    }
    
}
