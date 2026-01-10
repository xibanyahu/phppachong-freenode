<?php

declare(strict_types=1);

namespace App\Services\Cron;

//use App\Services\Cron;
use App\Models\HttpQueue as ModelHttpQueue;
use App\Services\TomTool\Telegram\Master as TelegramMaster;
use App\Services\TomTool\Telegram\Slave as TelegramSlave;
use App\Services\TomTool\HttpV2;
use Telegram\Bot\Api;
//use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Exceptions\TelegramResponse;

final class HttpQueue
{
    
    public static function pop()
    {
        echo "\nhttpQueue::pop - 开始\n";
        $oHttpQueue = ModelHttpQueue::where("status", 1)->get();
        
        foreach ($oHttpQueue as $oHttpQueueRow) {
            
            $aArg = json_decode($oHttpQueueRow->arg, true);
            
            $sBotCode = $aArg["sBotCode"] ?? '';
            $sChatCode = $aArg["sChatCode"] ?? '';
            $sBotToken = $aArg["sBotToken"] ?? '';
            $iChatId = $aArg["iChatId"] ?? 0;
            $sMsg = $aArg["sMsg"] ?? '';
            $sPhotoUrl = $aArg["sPhotoUrl"] ?? '';
            $iPhotoWidth = $aArg["iPhotoWidth"] ?? 0;
            $iPhotoHeight = $aArg["iPhotoHeight"] ?? 0;
            $enableMaster = $aArg["enableMaster"] ?? null;
            $sType = $aArg["sType"] ?? '';
                
            if ($enableMaster) {
                $oTgSlave = TelegramSlave::start()
                    ->enableDetail(false)
                    ->enableMaster(true)
                    ->enableAsync(false)
                    ->enableSlaveQueue(false)
                    ->setBotCode($sBotCode)
                    ->setChatCode($sChatCode)
                    ->setBotToken($sBotToken)
                    ->setChatId($iChatId)
                    ->setMsgS($sMsg)
                    ->setPhotoUrl($sPhotoUrl, $iPhotoWidth, $iPhotoHeight)
                    ->setType($sType)
                    ->send();
                
                if ($oTgSlave->isFail()) {
                    echo "失败：从telegram_local发送到master失败\n";
                    var_dump($oTgSlave->getResult());
                    continue;
                }
                
                echo "发送到master成功\n";
            } else { // 直接发送到tg，必须有token和id
                $oTgSlave = TelegramSlave::start()
                    ->enableDetail(false)
                    ->enableMaster(false)
                    ->setBotCode($sBotCode)
                    ->setChatCode($sChatCode)
                    ->setBotToken($sBotToken)
                    ->setChatId($iChatId)
                    ->setMsgS($sMsg)
                    ->setPhotoUrl($sPhotoUrl, $iPhotoWidth, $iPhotoHeight)
                    ->setType($sType)
                    ->send();
                
                if ($oTgSlave->isFail()) {
                    echo "失败：从telegram_local发送到tg失败\n";
                    var_dump($oTgSlave->getResult());
                    continue;
                }
                
                echo "发送到tg成功\n";
            }
            
            $oHttpQueueRow->delete();
        }
        
        echo "ok\n";
        
    }
    
}
