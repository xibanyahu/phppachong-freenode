<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tg;

//use App\Models\Article;
use App\Services\TomTool\Http;
use App\Services\Tg\Http as TGHttp;
use App\Models\Hook as ModelHook;
use App\Services\TomTool\ThrowableHandler;
//use App\Services\TomTool\TelegramVdb;
use App\Services\TomTool\Telegram\Slave as TelegramSlave;

final class HookController
{
    
    private $sCustomText = '';
    
    public function cmd_ffq_article()
    {
        try {
            
            $sUpdate = file_get_contents("php://input");
            $aUpdate = json_decode($sUpdate, true);
            $sText = $aUpdate["message"]["text"] ?? "没有text";
            if (!str_starts_with($sText, '/')) {
    //            $this->sCustomCmd = "article";
    //            $this->sCustomFunc = "cache";
    //            $this->aCustomOption[1] = "ffq";
                $this->sCustomText = "/article#cacheAdd__ffq_tg_a"." ".$sUpdate;
            }
            
            $this->cmd();
            
        } catch (\Throwable $e) {
            
            $aDataTg = ThrowableHandler::make($e)->enableTrace()->fetch();
            TelegramSlave::fail()->enableSlaveQueue(false)->setMsgA([
                                                                    "msg"   => $aDataTg,
                                                                    "data"  => $sUpdate,
                                                                    ])->send();
            
            $aDataCmd = $aDataTg;
            $sDataCmd = json_encode($aDataCmd, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $sDataCmd = str_replace("\\n", "<br>", $sDataCmd);
            echo $sDataCmd;
            exit;
        }
    }
    
    public function cmd()
    {
        
        try {
                
            $oTGHttp = new TGHttp();
            
            $oTGHttp->sApiToken = env('telegram_token');
            $oTGHttp->sApiChatId = env('telegram_chatid');
            
            $sUpdate = file_get_contents("php://input");
            
            $aUpdate = json_decode($sUpdate, true);
        
//            @file_put_contents('/www/_tg/bot_log.txt', json_encode($aUpdate, JSON_PRETTY_PRINT));
        
            $aData['chatId'] = (string) $aUpdate["message"]["chat"]["id"];
            $aData['fromId'] = (string) $aUpdate["message"]["from"]["id"];
            
            if ($this->sCustomText) {
                $aUpdate["message"]["text"] = $this->sCustomText;
            }
            
            if (!isset($aUpdate["message"]["text"])) {
                throw new \Exception(json_encode($aUpdate));
            }
            
            $sText = $aUpdate["message"]["text"];
            
            // hook优先
            // 非is_hook，并且，首字符非/
            if ((!isset($aUpdate["is_hook"]) || $aUpdate["is_hook"] == false) && substr($sText, 0, 1) !== "/") {
                
                $sHookKey = ModelHook::where("tg_user_id", $aData["fromId"])->first()?->key;
                
                if ($sHookKey) {
                    if ($sText == "run") {
                        $sText = $sHookKey;
                    } else {
                        $sText = $sHookKey.$sText;
                    }
                    $aUpdate["is_hook"] = true;
                }
                
            }

            @$aArg = explode(" ", $sText) ?? [];
            @$aOption = explode("__", $aArg[0]) ?? [];
            
            $sCmd = $this->cleanCmd($aOption[0]);
            
            // 大于3个说明是hook或dove之类
            $aCmd = explode("#", $sCmd);
//            if (count($aCmd) >= 3) {
//                $sHook = $aCmd[0];
//                $sClass = $sHook;
//                $sFunc = "go";
//            } else {
                $sClass = $aCmd[0] ?? false;
                $sFunc = $aCmd[1] ?? false;
//            }
            
            if (strpos($sClass, "dove$") !== false) {
                $sClass = "dove";
                $sFunc = "go";
            }
                
            $sUClass = ucfirst($sClass);
            $sUFunc = ucfirst($sFunc);
        
            unset($aOption[0]);
            unset($aArg[0]);
            
            $aParams = [];
            $aParams['aOption'] = $aOption;
            $aParams['aArg'] = $aArg;
            
            $sClassPath = "App\\Services\\Tg\\Hook\\Mod\\".$sClass;
            $sUClassPath = "App\\Services\\Tg\\Hook\\Mod\\".$sUClass;
        
            $oInstans = new $sUClassPath($aUpdate);
            $r = $oInstans->$sFunc($aParams, $aUpdate);
            
            $oTGHttp->send($r, $aUpdate);

            echo $r;
            
        } catch (\Throwable $e) {
            
            $aDataTg = ThrowableHandler::make($e)->enableTrace()->fetch();
            TelegramSlave::fail()->enableSlaveQueue(false)->setMsgA($aDataTg)->send();
            
            $aDataCmd = $aDataTg;
            $sDataCmd = json_encode($aDataCmd, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $sDataCmd = str_replace("\\n", "<br>", $sDataCmd);
            echo $sDataCmd;
            exit;
            
            // old
//            $sMessage = "错误（文件: " . $e->getFile() . "，行: " . $e->getLine() . "）: " . $e->getMessage();
//            $oTGHttp->send($sMessage, $sUpdate);
//            //Http::response(200, 'error'); 注释的
            // old end
            

        }
    }
    
    private function cleanCmd($input) {
        
        $input = str_replace('\\', '', $input);
        $input = str_replace('/', '', $input);
        
        return $input;
    }
    
}

