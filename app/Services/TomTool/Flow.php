<?php

declare(strict_types=1);

namespace App\Services\TomTool;

use App\Services\TomTool\TelegramVdb;

/**
 
 默认：
    name:none
    isDone:false
 
 new:
    $oResult = Flow::make("name");

 快速：
    Flow::make("name")->fail("kkkkkkk");
 
 进阶：
    Flow::make("name")->noDone()->msg("哟哟哟哟哟哟")->sendTg()->fetch();
 
 业务：
    step()
    msg()
    data()
    aData()
    jData()
    sData()
    code()
    put()
    merge()
    toDone()
    noDone()
    step("start")
 
 action:
    reset()
    done(?$msg)
    fail(?$msg, ?$aParentResult)
    sendTg() 伪action
    fetch() or get()
    fetchAll() or geAll()
 
 set：
    enableTg();
    parent($aParentResult);
 
 with:
    withTraceId();
    withTimestamp();
    withStep();
 
 判断：
    ::isDone($aResult);
    ::isFail($aResult);
 
 */

class Flow
{
    protected array $aStep = [];
    protected array $aParent = [];
    protected bool $isToTg = false;
    protected bool $isToTgOne = false;
    protected array $aResult = [
        'isDone' => false,
    ];
    protected string $sName;
    private bool $isChain = false;

    public function __construct(string $name = 'none')
    {
        $this->sName = $name;
        $this->aStep[] = "{$this->sName}::start";
    }

    // new
    public static function make(string $name = 'none'): self
    {
        return new self($name);
    }

    // new
    public static function start($sName = 'none')
    {
        $oInstance = new self($sName);
        $oInstance->isChain = true;
        return $oInstance;
    }
    
    // option
    public function enableTg(bool $flag = true): self
    {
        $this->isToTg = $flag;
        return $this;
    }

    // option
    public function enableTgOne(bool $flag = true): self
    {
        $this->enableTg(true);
        $this->isToTgOne = $flag;
        return $this;
    }

    // option
    public function parent(array $aParentResult): self
    {
        $this->aParent = $aParentResult;
        return $this;
    }

    // data
    public function step(string $step): self
    {
        $this->aStep[] = "{$this->sName}::{$step}";
        return $this;
    }

    // data
    public function msg($msg = "msg未设置"): self
    {
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $this->put('sMsg', $msg);
        return $this;
    }

    // data
    public function aData(array $data): self
    {
        $this->put('aData', $data);
        return $this;
    }

    // data
    public function aDataPut($k, $v)
    {
        $this->aResult["aData"][$k] = $v;
        return $this;
    }

    // data
    public function sData(string $data): self
    {
        $this->put('sData', $data);
        return $this;
    }

    // data
    public function code(string $code): self
    {
        $this->put('sCode', $code);
        return $this;
    }

    // data
    public function put(string $key, mixed $value, $sDataKey = ''): self
    {
        if ($sDataKey) {
            $this->aResult[$sDataKey][$key] = $value;
        } else {
            $this->aResult[$key] = $value;
        }

        return $this;
    }

    // data
    public function merge(array $data): self
    {
        foreach ($data as $k => $v) {
            $this->put($k, $v);
        }
        return $this;
    }

    // data
    public function setDone(bool $b = true): self
    {
        $this->aResult['isDone'] = $b;
        return $this;
    }

    // data
    public function setFail(string $step = ''): self
    {
        if ($step) {
            $this->step($step);
        }
        $this->aResult['isDone'] = false;
        return $this;
    }

    // with
    public function withTraceId(string $id = null): self
    {
        $this->aResult['sTraceId'] = $id ?? uniqid('trace_', true);
        return $this;
    }

    // with
    public function withTimestamp(): self
    {
        $this->aResult['sTimestamp'] = (new \DateTime())->format(\DateTime::ATOM);
        return $this;
    }

    // with
    public function withStep(bool $flag = true): self
    {
        if ($flag) {
            $this->withParentStep();
            $this->aResult['aStep'] = $this->aStep;
        } else {
            unset($this->aResult['aStep']);
        }
        return $this;
    }

    // action
    public function reset(): self
    {
        $this->aStep = [$this->sName . '::start'];
        $this->aParent = [];
        $this->isToTg = false;
        $this->isToTgOne = false;
        $this->aResult = ['isDone' => false];
        return $this;
    }

    // action
    public function done(mixed $msg = null)
    {
        $this->setDone();
        if ($msg) {
            $this->msg($msg);
        }
        return $this->get();
    }

    // action
    public function fail(mixed $msg = null, array $aParentResult = [])
    {
        $this->setFail('fail');
        if ($aParentResult) {
            $this->aParent = $aParentResult;
        }
        if ($msg) {
            $this->msg($msg);
        }
        return $this->getFull();
    }

    // action
//    public function sendTg() // 伪装成非set
//    {
//        $this->enableTgOne();
//        return $this;
//    }
    
    // action
    public function sendTg($sBotCode = 'base', $sChatCode = 'base')
    {
        
    }
    
    public function getDataA()
    {
        return $this->aResult["aData"] ?? [];
    }
    
    public function getDataS()
    {
        return (string) $this->aResult["sData"] ?? '';
    }
    
    public function getDataI()
    {
        return (int) $this->aResult["iData"] ?? 0;
    }
    
    public function getDataB()
    {
        return (bool) $this->aResult["bData"] ?? false;
    }
    
    public function getDataX()
    {
        return $this->aResult["xData"] ?? '';
    }
    
    public function setDataA(array $aData): self
    {
        $this->aData($aData);
        return $this;
    }
    
    public function setDataB(bool $bData): self
    {
        $this->aResult["bData"] = $bData;
        return $this;
    }
    
    public function setDataS(string $sData): self
    {
        $this->aResult["sData"] = $sData;
        return $this;
    }
    
    public function setDataI(int $iData): self
    {
        $this->aResult["iData"] = $iData;
        return $this;
    }
    
    public function setDataX($xData): self
    {
        $this->aResult["xData"] = $xData;
        return $this;
    }
    
    public function getResult(): array
    {
        return $this->aResult;
    }
    
    // action
    public function get()
    {
        if ($this->isToTg) {
            $this->_sendTg();
        }

        if ($this->isToTgOne) {
            $this->isToTg = false;
            $this->isToTgOne = false;
        }

        if ($this->isChain) {
            return $this;
        }

        return $this->aResult;
    }

    // action
    public function getFull()
    {
        $this->withStep();
        return $this->get();
    }

    // action
    public function toArray()
    {
        return $this->aResult;
    }

    // helper
    public function isDone()
    {
        return $this->aResult["isDone"] ?? false;
    }

    // helper
    public function isFail()
    {
        return !$this->isDone();
    }
    
    // helper
    public static function isDoneResult(array $aResult): bool
    {
        return $aResult['isDone'] ?? false;
    }

    // helper
    public static function isFailResult(array $aResult): bool
    {
        return !self::isDoneResult($aResult);
    }

    // shell
    public function fetch()
    {
        return $this->get();
    }

    // shell
    public function fetchAll()
    {
        return $this->getFull();
    }

    private function _sendTg()
    {
        TelegramVdb::make()->dataArr($this->aResult)->save();
    }

    protected function addStepParent(): void
    {
        $this->aStep[] = $this->aParent;
    }

    protected function withParentStep(): void
    {
        if ($this->aParent) {
            $this->addStepParent();
        }
    }
}
