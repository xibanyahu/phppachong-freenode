<?php

namespace App\Services\Tg\Hook\Mod;
//use App\Services\TomTool\Http;
//use App\Models\Article as ModelArticle;
use App\Services\TomTool\Http;
use App\Services\Tg\Http as TGHttp;

class Base {
    
    public function __construct() {
        $this->oHttp = new Http();
        $this->oTGHttp = new TGHttp();
//        $this->oTGHttp->sApiToken = env("telegram_token");
//        $this->oTGHttp->sApiChatId = env("telegram_chatid");
    }
    
    public function toJson($aData)
    {
        return json_encode($aData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    public function eachSendTg($aData)
    {
        foreach ($aData as $row) {
            $this->oTGHttp->sendToTG(json_encode($row, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        
        $this->oHttp::response(200, 'ok');
    }
    
//    protected function cmdGood($sCmd) {
//        
//        $sCmdNew = "";
//        switch ($sCmd) {
//            case "user":
//                $sCmdNew = "user_name";
//                break;
//            case "project":
//                $sCmdNew = "project_name";
//                break;
//            case "code":
//                $sCmdNew = "sale_code";
//                break;
//            case "condition":
//                $sCmdNew = "cron_condition";
//                break;
//            default:
//                $sCmdNew = $sCmd;
//                break;
//        }
//        
//        return $sCmdNew;
//        
//    }
    
}
