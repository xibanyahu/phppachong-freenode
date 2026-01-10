<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HttpQueue as ModelHttpQueue;
use App\Services\TomTool\Flow;

final class HttpQueue
{
    public $sName;
    public $aArg = [];
    public $sUrl;
    
    public static function start(string $sName = ''): self
    {
        $oInstance = new self();
        $oInstance->sName = $sName;
        
        return $oInstance;
    }
    
    public function url($sUrl)
    {
        $this->sUrl = $sUrl;
        
        return $this;
    }
    
    public function arg($aArg)
    {
        $this->aArg = $aArg;
        
        return $this;
    }
    
    public function save(): Flow
    {
        $oResult = Flow::start("httpQueue");
        
        $oModelHttpQueue = new ModelHttpQueue();
        $oModelHttpQueue->name = $this->sName;
        $oModelHttpQueue->url = $this->sUrl;
        $oModelHttpQueue->arg = json_encode($this->aArg, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $r = $oModelHttpQueue->save();
        
        if (!$r) {
            return $oResult->fail("进入队列失败");
        }
        
        return $oResult->done("成功");
    }
    
}
