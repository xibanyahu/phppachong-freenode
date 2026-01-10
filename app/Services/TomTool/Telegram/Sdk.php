<?php

declare(strict_types=1);

namespace App\Services\TomTool\Telegram;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Exceptions\TelegramSDKException;
use App\Services\TomTool\Flow;
//use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Services\TomTool\HttpV2;

class Sdk
{
    public $sMsgFormat = [];
    public $sMsgFormatType = 'none';
    public $sBotToken = '';
    public $iChatId = 0;
    public $sPhotoUrl = '';
    public $iPhotoWidth = 0;
    public $iPhotoHeight = 0;
    public $sFileUrl = '';
    public $sFileName = '';
    public $sMsg;
    public $oApi;

    private const ALLOWED_FORMATS = ['none', 'html', 'markdown', 'markdownV2'];
    
    public static function start()
    {
        $oInstance = new self();
        return $oInstance;
    }
    
    public function setBotToken($sBotToken)
    {
        $this->oApi = new Api($sBotToken);
        $this->sBotToken = $sBotToken;
        return $this;
    }
    
    public function setChatId($iChatId)
    {
        $this->iChatId = $iChatId;
        return $this;
    }
    
    public function setMsgS($sMsg = '')
    {
        $this->sMsg = $sMsg;
        return $this;
    }
    
    public function setPhotoUrl($sUrl = '', $iPhotoWidth = 0, $iPhotoHeight = 0)
    {
        $this->sPhotoUrl = $sUrl;
        $this->iPhotoWidth = $iPhotoWidth;
        $this->iPhotoHeight = $iPhotoHeight;
        return $this;
    }
    
    public function setFileUrl($sFileUrl = '', $sFileName = '')
    {
        $this->sFileUrl = $sFileUrl;
        $this->sFileName = $sFileName;
        return $this;
    }
    
    public function setType($sType)
    {
        if ($sType) {
            $this->sMsgFormatType = $sType;
        }
        
        return $this;
    }
    
    public function send()
    {
        $oFlow = Flow::start("tg_sdk_send");
        
        $this->msgFormat();

//        try {

            if ($this->sPhotoUrl) {
                $r = $this->oApi->sendPhoto($this->sMsgFormat);
            } else if ($this->sFileUrl) {
                $r = $this->oApi->sendDocument($this->sMsgFormat);
            } else {
                $r = $this->oApi->sendMessage($this->sMsgFormat);
            }

            if (!($r && $r->getMessageId())) {
                return $oFlow->fail($r);
            }
//        } catch (TelegramSDKException $e) {
//            return $oFlow->fail(['error' => $e->getMessage()]);
//        }

        return $oFlow->done();
    }
    
    public function getMsgFormat()
    {
        $this->msgFormat();
        
        return $this->sMsgFormat;
    }
    
    public function msgFormat()
    {
        if (!in_array($this->sMsgFormatType, self::ALLOWED_FORMATS, true)) {
            throw new \RuntimeException("不支持的格式类型: {$this->sMsgFormatType}");
        }

        $method = 'msgFormat_' . $this->sMsgFormatType;
        $this->sMsgFormat = $this->$method();
        
        if ($this->sPhotoUrl) {

            $oManager = new ImageManager(new Driver());
            
            $sTempPath = $_ENV['dir_base'].'public/upload';
            $sTempPath .= "/tg_output.png";
            
            $sImg = file_get_contents($this->sPhotoUrl);
            $oImage = $oManager->read($sImg);

            if ($this->iPhotoWidth && $this->iPhotoHeight) {
                $oImage = $oImage->cover($this->iPhotoWidth, $this->iPhotoHeight);
            }

            $oImage->toJpg(80)->save($sTempPath);
            
            $photo = InputFile::create($sTempPath, date("Y-m-d").'.jpg');
            
            $this->sMsgFormat["photo"] = $photo;
            
            $this->sMsgFormat["caption"] = $this->sMsgFormat["text"];
            unset($this->sMsgFormat["text"]);
            
        } else if ($this->sFileUrl) {
            
            if (!$this->sFileName) {
                $sFileName = date("Y-m-d H:i:s").'.txt';
            } else {
                $sFileName = $this->sFileName;
            }
            
            $sFile = InputFile::create($this->sFileUrl, $sFileName);
            
            $this->sMsgFormat["document"] = $sFile;
            $this->sMsgFormat["caption"] = $this->sMsgFormat["text"];
            unset($this->sMsgFormat["text"]);
            
        }

        return $this;
    }

    private function msgFormat_none()
    {
        return [
            'chat_id' => $this->iChatId,
            'text' => $this->sMsg,
            'parse_mode' => '',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null,
        ];
    }

    private function msgFormat_html()
    {
        return [
            'chat_id' => $this->iChatId,
            'text' => strip_tags($this->sMsg, [
                'b', 'strong', 'i', 'em', 'u', 'ins', 's', 'strike', 'del', 'span', 'tg-spoiler', 'a', 'tg-emoji',
                'code', 'pre',
            ]),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null,
        ];
    }

    private function msgFormat_markdown()
    {
        return [
            'chat_id' => $this->iChatId,
            'text' => $this->sMsg,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null,
        ];
    }

    private function msgFormat_markdownV2()
    {
        return [
            'chat_id' => $this->iChatId,
            'text' => $this->sMsg,
            'parse_mode' => 'MarkdownV2',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null,
        ];
    }
    
}
