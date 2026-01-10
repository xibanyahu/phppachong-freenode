<?php

namespace App\Services\Tg\Hook\Mod;

//use App\Models\Hook as ModelHook;
use App\Models\DoveSite as ModelDoveSite;
use App\Services\TomTool\Http;
use App\Services\Tg\Http as TGHttp;
use App\Services\Tg\Hook\Dog;

class Dove extends Base {
    
    private $oDog;

    public function __construct($aUpdate)
    {
        
        $this->oDog = new Dog(new ModelDoveSite());
        $this->oDog->_setDogName("dove"); // 必须，me用，正常就是类名，只不过后期没准改名，所以就这里写死
        $this->oDog->_setPrimaryKey("code"); // 可选，默认id
//        $this->oDog->_setStrField(["content"]); // 可选，指定长文本字段
    }
    
    public function __call($sName, $aArg)
    {
        
        return $this->oDog->$sName($aArg[0]);
        
    }
    
    public function go($aParam, $aData)
    {
        $oDoveSiteCheckedList = ModelDoveSite::where("dove_checked", 1)->get();

        list(, $sSubCmd) = explode("$", $aData['message']['text'], 2);

        $aData['message']['text'] = $sSubCmd;
        
        $aData['is_dove'] = 1;
        
        $aData['dove_token'] = env('dove_token');
        
        $oTGHttp = new TGHttp();
        $oHttp = new Http();
        $oHttp->sMethod = "post";
        $oHttp->bIsJson = true;
        $r = [];
        foreach ($oDoveSiteCheckedList as $row) {
            
            $sApiUrl = "https://".$row->www.$row->api_path;
            $oHttp->sToUrl = $sApiUrl;
            $oHttp->aData = $aData;
            $tmp = $oHttp->send();
            
            $r = "***".$row->code."*** \n\n".$tmp;
            $oTGHttp->sendToTG($r);
            
            
        }
        
        Http::response(200);
    }
    
//    public function del($aParam)
//    {
//        $sCode = $aParam["aArg"][1] ?? false;
//        
//        if (!$sCode) {
//            return "要code";
//        }
//        
//        $oDove = ModelDoveSite::where("code", $sCode)->first();
//        
//        if (!$oDove) {
//            return "没有";
//        }
//        
//        $oDove->delete();
//        
//        return $this->list();
//    }
    
//    public function add($aParam)
//    {
//        $sCode = $aParam["aArg"][1];
//        
//        $oDove = new ModelDoveSite();
//        
//        $oDove->code = $sCode;
//        
//        $oDove->save();
//        
//        return $this->list();
//    }
    
//    public function set($aParam)
//    {
//        $sCode = $aParam["aOption"][1] ?? false;
//        $k = $aParam["aOption"][2] ?? false;
//        $v = $aParam["aArg"][1] ?? false;
//
//        if (!$sCode) {
//            return "需要code";
//        }
//        
//        $oDove = ModelDoveSite::where("code", $sCode)->first();
//        
//        if (!$oDove) {
//            return "没有";
//        }
//        
//        $oDove->$k = $v;
//        
//        $oDove->save();
//        
//        return $this->toJson($oDove);
//    }
    
//    public function list()
//    {
//        $oDove = ModelDoveSite::all();
//        
//        $r = $this->toJson($oDove);
//        
//        return $r;
//    }
    
//    public function class($aParam)
//    {
//        $sClass = $aParam["aArg"][1] ?? false;
//        
//        if ($sClass == "?") {
//            return "/dove#class <class>";
//        }
//        
//        if (!$sClass) {
//            return "需要class";
//        }
//        
//        $aParam["aOption"][1] = "class";
//        $aParam["aArg"][1] = $sClass;
//        
//        return $this->where($aParam);
//    }
    
//    public function where($aParam)
//    {
//        $sField = $aParam["aOption"][1] ?? false;
//        $sValue = $aParam["aArg"][1] ?? false;
//        
//        if ($sValue == "?") {
//            return "/dove#where__<firld> <value>";
//        }
//        
//        if (!$sField) {
//            return "需要field";
//        }
//        
//        if (!$sValue) {
//            return "需要value";
//        }
//        
//        $oDoveSite = ModelDoveSite::where($sField, $sValue)->get();
//        
//        if ($oDove->isEmpty()) {
//            return "没找到这个";
//        }
//        
//        return $this->toJson($oDoveSite);
//    }
    
    public function check($aParam)
    {
        $iSetValue = $aParam['iSetValue'] ?? 1;
        $sCode = $aParam['aArg'][1];
        
        $oDoveSite = ModelDoveSite::where("code", $sCode)->first();

        if (!$oDoveSite) {
            throw new \Exception("没有这个code");
        }
        
        $oDoveSite->dove_checked = $iSetValue;
        
        $r = $oDoveSite->save();
        
        if (!$r) {
            throw new \Exception("没check上？");
        }
        
        $r = $this->now();
        
        return $r;
    }
    
    public function checkOne($aParam)
    {
        ModelDoveSite::query()->update(["dove_checked" => 0]);
        
        return $this->check($aParam);
    }
    
    public function checkClear()
    {
        
        $r = ModelDoveSite::query()->update(["dove_checked" => 0]);
        
        return $this->list();
    }
    
    public function checkUn($aParam)
    {
        $sCode = $aParam["aArg"][1];
        
        $r = ModelDoveSite::where("code", $sCode)->update(["dove_checked" => 0]);
        
        return $this->now();
    }
    
    public function now()
    {
        $aDoveSiteNow = ModelDoveSite::where("dove_checked", 1)->get()->toArray();
        
//        if ($aDoveSiteNow) {
//            $r = "";
//            foreach ($aDoveSiteNow as $row) {
//                $r .= "\n";
//                $r .= $row['code']."::".$row['www']."::".$row['class'];
//            }
//        } else {
//            $r = "没有";
//        }
        
        if (!$aDoveSiteNow) {
            return "没有";
        }
        
        $r = $this->toJson($aDoveSiteNow);
        
        return $r;
    }
    
}
