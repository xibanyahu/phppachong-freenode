<?php

declare(strict_types=1);

namespace App\Services\TomTool\Telegram;

use App\Services\HttpQueue as ServiceHttpQueue;
use App\Services\TomTool\Flow;
use App\Services\TomTool\Telegram\Sdk as ServiceTelegramSdk;
use App\Models\TelegramKey as ModelTelegramKey;

/**
 
 注意是master服务器端专用的，这里会对code找map，slave不会。
 
 new:
    start(?$sBodeCode, ?$sChatCode)
 
 set:
    setBotCode()
    setChatCode()
    setBotToken()
    setChatId()
    setMsgS()

 action:
    send()
 
 */

final class Master
{
    public string $sBotCode = "base";
    public string $sChatCode = "base";
    public string $sBotToken = '';
    public int $iChatId = 0;
    
    public string $sMsg = '';
    public string $sPhotoUrl = '';
    public int $iPhotoWidth = 0;
    public int $iPhotoHeight = 0;
    
    public string $sFileUrl = '';
    public string $sFileName = '';
    
    public string $sType = '';
    
    public bool $enableQueue = false; // 应该用不到了
    
    public bool $bKeyBegin = false;
    
    public static function start($sBotCode = 'base', $sChatCode = 'base'): self
    {
        $oInstance = new self();
        $oInstance->sBotCode = $sBotCode;
        $oInstance->sChatCode = $sChatCode;
        return $oInstance;
    }
    
    public function setBotCode(string $sBotCode): self
    {
        $this->sBotCode = $sBotCode;
        return $this;
    }
    
    public function setChatCode(string $sChatCode): self
    {
        $this->sChatCode = $sChatCode;
        return $this;
    }
    
    public function setBotToken(string $sBotToken): self
    {
        $this->sBotToken = $sBotToken;
        return $this;
    }
    
    public function setChatId(int $iChatId): self
    {
        $this->iChatId = $iChatId;
        return $this;
    }
    
    public function setMsgS(string $sMsg): self
    {
        $this->sMsg = $sMsg;
        return $this;
    }
    
    public function setPhotoUrl(string $sPhotoUrl, int $iPhotoWidth = 0, int $iPhotoHeight = 0): self
    {
        $this->sPhotoUrl = $sPhotoUrl;
        $this->iPhotoWidth = $iPhotoWidth;
        $this->iPhotoHeight = $iPhotoHeight;
        return $this;
    }
    
    public function setType(string $sType): self
    {
        $this->sType = $sType;
        return $this;
    }
    
    public function setFileUrl(string $sFileUrl = '', string $sFileName = ''): self
    {
        $this->sFileUrl = $sFileUrl;
        $this->sFileName = $sFileName;
        return $this;
    }
    
    public function send($sMsg = ''): Flow
    {
        if ($sMsg) {
            $this->sMsg = $sMsg;
        }
        
        if (!$this->sMsg) {
//            throw new \Exception("未设置msg");
        }

        $this->keyBegin();
        
        if ($this->enableQueue) {
            $aData = $this->buildDataTom();
            $oFlow = ServiceHttpQueue::start("telegram_local")->arg($aData)->save();
        } else {
            $oFlow = ServiceTelegramSdk::start()
                        ->setBotToken($this->sBotToken)
                        ->setChatId($this->iChatId)
                        ->setMsgS($this->sMsg)
                        ->setPhotoUrl($this->sPhotoUrl, $this->iPhotoWidth, $this->iPhotoHeight)
                        ->setFileUrl($this->sFileUrl, $this->sFileName)
                        ->setType($this->sType)
                        ->send();
        }

        return $oFlow;
    }
    
    private function keyBegin()
    {
        if (!$this->bKeyBegin) {
            if (!$this->sBotToken) {
                $this->sBotToken = ModelTelegramKey::botTokenByCode($this->sBotCode);
            }

            if (!$this->iChatId) {
                $this->iChatId = ModelTelegramKey::chatIdByCode($this->sChatCode);
            }

            if (!$this->sBotToken || !$this->iChatId) {
                throw new \RuntimeException("botToken或chatId没找到，botCode={$this->sBotCode}，chatCode={$this->sChatCode}");
            }

            $this->bKeyBegin = true;
        }
    }
    
    private function buildDataTom(): array
    {
        return [
            "sBotCode"  => $this->sBotCode ?? '',
            "sChatCode" => $this->sChatCode ?? '',
            "sBotToken" => $this->sBotToken ?? '',
            "iChatId"   => $this->iChatId ?? '',
            "sPhotoUrl" => $this->sPhotoUrl ?? '',
            "iPhotoWidth" => $this->iPhotoWidth,
            "iPhotoHeight" => $this->iPhotoHeight,
            "sFileUrl"  => $this->sFileUrl,
            "sFileName" => $this->sFileName,
            "sMsg"      => $this->sMsg ?? '',
            "sType"     => $this->sType ?? '',
        ];
    }
    
    
}
