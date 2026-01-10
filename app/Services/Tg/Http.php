<?php

namespace App\Services\Tg;
use App\Services\TomTool\Http as TomHttp;

class Http {
    
    public $sApiToken;
    public $sApiChatId;
    
    function __construct()
    {
        $this->sApiToken = env('telegram_token');
        $this->sApiChatId = env('telegram_chatid');
    }
    
    public function send($sMsg = "msg空", $aUpdate = [])
    {
        
        $bSendTg = true;
        
        $sShell = $aUpdate["shell"] ?? false;
        $bIsDove = $aUpdate["is_dove"] ?? false;
        
        if ($sShell == "cmd") {
            $bSendTg = false;
        }
        
        if ($bIsDove == true) {
            $bSendTg = false;
        }
        
        if (env("is_loc") == true) {
            $bSendTg = false;
        }
        
        if (is_array($sMsg)) {
            if ($bSendTg) {
                foreach ($sMsg as $sMsgRow) {
                    $this->sendTg($sMsgRow);
                }
            } else {
                echo json_encode($sMsg, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        } else {
            if ($bSendTg) {
                $this->sendTg($sMsg);
            } else {
                echo $sMsg;
            }
        }
        
        if ($bSendTg) {
            TomHttp::response(200);
        } else {
            exit;
        }
        
    }
    
    private function sendTg($sMsg)
    {
        $oHttp = new TomHttp();
        $oHttp->sMethod = "post";
        $oHttp->sToUrl = "https://api.telegram.org/bot$this->sApiToken/sendMessage";
//            $oHttp->bIsJson = true;
        $oHttp->aData = [
            "chat_id"   => $this->sApiChatId,
            "text"      => $sMsg
        ];
        
        $oHttp->send();
    }
    
    public function sendToTG($sMsg, $aUpdate = [])
    {
        
        if (isset($aUpdate["shell"]) && $aUpdate["shell"] == "cmd") {
            
            echo $sMsg;
            
        } else if (isset($aUpdate["is_dove"]) && $aUpdate["is_dove"] == true) {
            
            echo $sMsg;
            
        } else {
            
            if (env("is_loc") == true) {
                echo $sMsg;
            } else {
                $oHttp = new TomHttp();
                $oHttp->sMethod = "post";
                $oHttp->sToUrl = "https://api.telegram.org/bot$this->sApiToken/sendMessage";
    //            $oHttp->bIsJson = true;
                $oHttp->aData = [
                    "chat_id"   => $this->sApiChatId,
                    "text"      => $sMsg
                ];
                
                $oHttp->send();
            }
            
        }
        
    }
    
    
}
