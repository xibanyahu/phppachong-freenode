<?php

declare(strict_types=1);

namespace App\Services\IM;

use App\Models\Config;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function strip_tags;

// 没有必须，默认使用base配置
final class TelegramV2
{
    private Api $bot;
    public $iTo;
    public $sToken;
    public $sMsgType = false;
    
    public function __construct($sToken = "base", $sTo = "base")
    {
        $this->setToken($sToken);
        
        $this->setTo($sTo);
        
//        $this->bot = new Api($this->sToken);
    }
    
    public function setMsgType($sMsgType)
    {
        $this->sMsgType = $sMsgType;
    }
    
    public function setTo($xTo, $sType = "code")
    {
        $iChatId = $xTo;
        
        if ($sType == "code") {
//            $sEnvKey = "tgChat_".$xTo;
            $iChatId = $_ENV['tgChat'][$iChatId];
        }
        
        $this->iTo = $iChatId;
    }
    
    public function setToken($xToken, $sType = "code")
    {
        $sToken = $xToken;
        
        if ($sType == "code") {
//            $sEnvKey = "tgToken_".$xToken;
            $sToken = $_ENV['tgToken'][$sToken];
        }
        
        $this->sToken = $sToken;
        
        $this->bot = new Api($this->sToken);
    }
    
    public function send($sMsg, $sMsgType = 'str')
    {
        if ($sMsgType == "arr") {
            $sMsg = json_encode($sMsg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        if ($this->sMsgType === false) {
            $sendMessage = [
                'chat_id' => $this->iTo,
                'text' => $sMsg,
                'parse_mode' => '',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];
        } else if ($this->sMsgType === "html") {
            $sendMessage = [
                'chat_id' => $this->iTo,
                'text' => strip_tags(
                 $sMsg,
                    ['b', 'strong', 'i', 'em', 'u', 'ins', 's', 'strike','del', 'span','tg-spoiler', 'a', 'tg-emoji',
                        'code', 'pre',
                    ]
                ),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];
        } else if ($this->sMsgType === "markdown") {
            $sendMessage = [
                'chat_id' => $this->iTo,
                'text' => $sMsg,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];
        } else if ($this->sMsgType === "markdownV2") {
            $sendMessage = [
                'chat_id' => $this->iTo,
                'text' => $sMsg,
                'parse_mode' => 'MarkdownV2',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];
        } else {
            $sendMessage = [
                'chat_id' => $this->iTo,
                'text' => $sMsg,
                'parse_mode' => '',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];
        }

        return $this->bot->sendMessage($sendMessage);
    }
    
}
