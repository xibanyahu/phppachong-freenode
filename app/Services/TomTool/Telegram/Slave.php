<?php

// 版本 25/12/12

declare(strict_types=1);

namespace App\Services\TomTool\Telegram;

use App\Services\TomTool\HttpV2;
use App\Services\TomTool\Telegram\Sdk as ServiceTelegramSdk;
use App\Services\TomTool\Flow;
use App\Services\HttpQueue as ServiceHttpQueue;

/**
 
 提醒：
    master自身已经没有队列了，收到slave消息直接转发
 
 默认：
    走本地队列，发送到master
    走异步
 
 new:
    start()
    log()
    warn()
    fail()
    notify()
    
 opt:
    enableMaster(:true)
    enableMasterQueue(:true)
    enableSlaveQueue(:true)
    enableDetail(:true)
 
 set:
    setMsgS()
    setMsgA()
    setBotCode()
    setChatCode()
    setBotToken()
    setChatId()
    
 action:
    send()
    sendMaster()
    sendLocal()
 
 */

class Slave
{
    public string $sBotCode;
    public string $sChatCode;
    
    public string $sBotToken = '';
    public int $iChatId = 0;
    
    public string $sMsg = '';
    public string $sPhotoUrl = '';
    public int $iPhotoWidth = 0;
    public int $iPhotoHeight = 0;
    
    public string $sFileUrl = '';
    public string $sFileName = '';
    
    public string $sType = '';
    
    public bool $enableMaster = true;
    public bool $enableMasterQueue = false; // 这里之前想错了，不会有master队列，只在slave有，master转发就行了
    public bool $enableSlaveQueue = false;
    
    public bool $enableDetail = true;
    
    public bool $enableAsync = true;
    
    private bool $enableSend = true;
    
    // new
    public static function start(string $sBotCode = 'base', string $sChatCode = 'base'): self
    {
        $oInstance = new self;
        $oInstance->sBotCode = $sBotCode;
        $oInstance->sChatCode = $sChatCode;
        $oInstance->enableSend = filter_var($_ENV["tg_send_enable"] ?? true, FILTER_VALIDATE_BOOLEAN);
        return $oInstance;
    }
    
    // new
    public static function log(string $sChatCode = 'admin'): self
    {
        return self::start("log", $sChatCode);
    }
    
    // new
    public static function warn(string $sChatCode = 'admin'): self
    {
        return self::start("warn", $sChatCode);
    }
    
    // new
    public static function fail(string $sChatCode = 'admin'): self
    {
        return self::start("fail", $sChatCode);
    }
    
    // new
    public static function notify(string $sChatCode = 'admin'): self
    {
        return self::start("fail", $sChatCode);
    }
    
    
    // opt
    public function enableAsync(bool $b = true): self
    {
        $this->enableAsync = $b;
        return $this;
    }
    
    // opt
    public function enableDetail(bool $b = true): self
    {
        $this->enableDetail = $b;
        return $this;
    }
    
    // opt
    public function enableSlaveQueue(bool $b = true): self
    {
        $this->enableSlaveQueue = $b;
        return $this;
    }
    
    // opt
    public function enableMaster(bool $b = true): self
    {
        $this->enableMaster = $b;
        return $this;
    }
    
    // opt
    public function enableMasterQueue(bool $b = true): self
    {
        $this->enableMasterQueue = $b;
        return $this;
    }
    
    // set
    public function setPhotoUrl(string $sPhotoUrl, int $iWidth = 0, int $iHeight = 0): self
    {
        $this->sPhotoUrl = $sPhotoUrl;
        $this->iPhotoWidth = $iWidth;
        $this->iPhotoHeight = $iHeight;
        return $this;
    }
    
    // set
    public function setFileUrl(string $sFileUrl, string $sFileName = ''): self
    {
        $this->sFileUrl = $sFileUrl;
        $this->sFileName = $sFileName;
        return $this;
    }
    
    // set
    public function setType(string $sType): self
    {
        $this->sType = $sType;
        return $this;
    }
    
    // set
    public function setMsgS(string $sMsg = ''): self
    {
        if ($this->enableDetail) {
            $sMsg = "[ ".$this->getEnvSiteCode()." | ".date("d:H:i:s")." ] ".$sMsg;
        }
        
        $this->sMsg = $sMsg;
        
        return $this;
    }
    
    // set
    public function setMsgA(array $aMsg = []): self
    {
        $aMsgNew = $aMsg;
        
        if ($this->enableDetail) {
            $aMsgNew = [];
            $aMsgNew["site_code"] = $this->getEnvSiteCode();
            $aMsgNew["date"] = date("Y-m-d H:i:s");
            $aMsgNew["data"] = $aMsg;
        }
        
        $this->sMsg = json_encode($aMsgNew, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return $this;
    }
    
    // set
    public function setBotCode(string $sBotCode): self
    {
        $this->sBotCode = $sBotCode;
        return $this;
    }
    
    // set
    public function setChatCode(string $sChatCode): self
    {
        $this->sChatCode = $sChatCode;
        return $this;
    }
    
    // set
    public function setBotToken(string $sBotToken): self
    {
        $this->sBotToken = $sBotToken;
        return $this;
    }
    
    // set
    public function setChatId(int $iChatId): self
    {
        $this->iChatId = $iChatId;
        return $this;
    }
    
    // action
    public function send($xMsg = ''): Flow
    {
        if ($this->enableSend == false) {
            $oFlow = Flow::start();
            return $oFlow->done("不发送");
        }
        
        if ($xMsg) {
            if (is_array($xMsg)) {
                $this->setMsgA($xMsg);
            } else {
                $this->setMsgS($xMsg);
            }
        }
        
        if (!$this->sMsg) {
//            throw new \Exception("未设置msg");
        }
        
        if ($this->enableMaster) {
            $oFlow = $this->_send_master();
        } else {
            $oFlow = $this->_send_local();
        }
        
        return $oFlow;
    }
    
    // action
    public function sendMaster($xMsg = ''): Flow
    {
        $this->enableMaster(true);
        return $this->send($xMsg);
    }
    
    // action
    public function sendLocal($xMsg = ''): Flow
    {
        $this->enableMaster(false);
        return $this->send($xMsg);
    }
    
    private function _send_master(): Flow
    {
        $aData = $this->buildDataTom();
        
        if ($this->enableSlaveQueue) {
            $oFlow = ServiceHttpQueue::start("telegram_master")->arg($aData)->save();
        } else {
            
            $oFlow = Flow::start("_send_master");
            $sUrlMaSterBase = $_ENV["url_master_base"] ?? "";
            
            if (!$sUrlMaSterBase) {
                throw new \Exception("未设置env的url_master_base");
            }
            
            $sUrlMasterPost = "https://".$sUrlMaSterBase."/api/telegram/post";

            try {
                
                $oHttpV2 = HttpV2::make($sUrlMasterPost);
                
                if ($this->enableAsync) {
                    $oHttpV2 = $oHttpV2->enableAsync();
                }
                
                $r = $oHttpV2->enableJsonAs()->setData($aData)->send();

                if ($this->enableAsync) {
                    return $oFlow->sData("async")->done("async");
                } else {
                    $a = json_decode($r, true);
                    $isDone = $a["isDone"] ?? false;
                    
                    if ($isDone) {
                        return $oFlow->done();
                    } else {
                        return $oFlow->fail($r);
                    }
                    
                }
                
            } catch (\Throwable $e) {
                $oFlow = $oFlow->fail($e->getMessage());
            }
        }
        
        return $oFlow;
    }
    
    // 直接从本地发到tg，几乎用不上，都走master了
    private function _send_local(): Flow
    {
        if ($this->enableSlaveQueue) {
            $aData = $this->buildDataTom();
            $oFlow = ServiceHttpQueue::start("telegram_local")->arg($aData)->save();
        } else {
            $oFlow = ServiceTelegramSdk::start($this->sBotToken, $this->iChatId)->setMsgS($this->sMsg)->send();
        }
        
        return $oFlow;
    }
    
    private function buildDataTom(): array
    {
        return [
            "sBotCode"  => $this->sBotCode ?? '',
            "sChatCode" => $this->sChatCode ?? '',
            "sBotToken" => $this->sBotToken ?? '',
            "iChatId"   => $this->iChatId ?? '',
            "enableMasterQueue"  => $this->enableMasterQueue ?? '',
            "enableMaster" => $this->enableMaster,
            "sMsg"    => $this->sMsg ?? '',
            "sPhotoUrl" => $this->sPhotoUrl ?? '',
            "iPhotoWidth"   => $this->iPhotoWidth ?? 0,
            "iPhotoHeight"  => $this->iPhotoHeight ?? 0,
            "sType" => $this->sType ?? '',
            "sFileUrl"  => $this->sFileUrl ?? '',
            "sFileName" => $this->sFileName ?? '',
        ];
    }
    
    private function getEnvSiteCode(): string
    {
        $sEnvSiteCode = $_ENV["site_code"] ?? "未设置env的site_code";
        
        return $sEnvSiteCode;
    }
    
}
