<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\TomTool\Telegram\Slave as TeleSlave;

class TelegramKey extends Model
{
    protected $table = 'telegram_key';
    public $timestamps = false;
    
    public static function botTokenByCode($sBotCode)
    {
        $sBotToken = self::where("type", "bot_token")->where("key", $sBotCode)->first()?->value;
        
        if (!$sBotToken) {
//            TeleSlave::notify()->send("未映射botCode：".$sBotCode."，使用默认base。");
            $sBotToken = self::where("type", "bot_token")->where("key", "base")->first()?->value;
        }
        
        return $sBotToken;
    }
    
    public static function chatIdByCode($sChatCode)
    {
        $iChatId = self::where("type", "chat_id")->where("key", $sChatCode)->first()?->value;
        
        if (!$iChatId) {
//            TeleSlave::notify()->send("未映射chatCode：".$sChatCode."，使用默认base。");
            $iChatId = self::where("type", "chat_id")->where("key", "base")->first()?->value;
        }
        
        return (int) $iChatId;
    }
    
}
