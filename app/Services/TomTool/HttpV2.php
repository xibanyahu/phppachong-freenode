<?php

// 版本：25-12-12

declare(strict_types=1);

namespace App\Services\TomTool;

use App\Services\TomTool\Flow;
use Exception;

/**
 
 默认：
    method:post
 
 new:
    HttpV2::make($url)
 
 基本：
    HttpV2::make($url)->send()
 
 进阶：
    HttpV2::make($url, "get")->setDataA($aData)->enableJsonAs()->enableAsync()->optMethod("get")->send();
 
 action:
    send()
 
 data:
    xData()
    sData()
    jData()
    sData()
 
 set:
    optMethod()
    enableJsonAs()
    enableJsonSend()
    enableJsonReturn()
 
 @note:
    默认关闭 SSL 验证（适用于开发环境），生产环境建议开启
    异步模式下不会返回响应内容，仅快速触发请求
 
 */


class HttpV2
{
    protected string $method;
    protected $data = [];
    protected string $url;
    protected bool $sendJson = false;
    protected bool $returnJson = false;
    protected bool $async = false;
//    private bool $isChain = false;
//    private array $aResult = [
//        "isDone" = false
//    ];

    public static function make(string $url, string $method = 'post'): self
    {
        $instance = new self();
        $instance->url = $url;
        $instance->optMethod($method);
        return $instance;
    }
    
//    public static function start(string $sUrl, string $sMethod = 'post'): self
//    {
//        $oInstance = new self();
//        $oInstance->url = $sUrl;
//        $oInstance->method = $sMethod;
//        $oInstance->isChain = true;
//        return $oInstance;
//    }
//    
//    public function isDone()
//    {
//        return $this->aResult["isDone"] ?? false;
//    }
//    
//    public function isFail()
//    {
//        return !$this->isDone();
//    }
    
//    public function getResult()
//    {
//        return $this->aResult;
//    }
    
    public function optMethod(string $method): self
    {
        $this->method = strtolower($method);
        return $this;
    }
    
    public function setDataX($xData)
    {
        $this->setData($xData);
        return $this;
    }
    
    public function setDataA($aData)
    {
        $this->setData($aData);
        return $this;
    }
    
    public function setDataS($sData)
    {
        $this->setData($sData);
        return $this;
    }
    
    public function setData($xData)
    {
//        if (is_array($xData)) {
//            json_encode($xData)
//        }
        $this->data = $xData;
        return $this;
    }

//    public function aData($aData = [])
//    {
//        $this->data($aData);
//        return $this;
//    }
//    
//    public function sData($sData = '')
//    {
//        $this->data($sData);
//        return $this;
//    }
//    
//    public function jData($jData = '')
//    {
//        $this->data($jData);
//        return $this;
//    }
//    
//    public function xData($xData = '')
//    {
//        $this->data($xData);
//        return $this;
//    }
//    
//    public function data($data = ''): self
//    {
//        $this->data = $data;
//        return $this;
//    }

    public function enableJsonSend(bool $flag = true): self
    {
        $this->sendJson = $flag;
        return $this;
    }

    public function enableJsonReturn(bool $flag = true): self
    {
        $this->returnJson = $flag;
        return $this;
    }
    
    public function enableJsonAs(): self
    {
        $this->sendJson = true;
        $this->returnJson = true;
        return $this;
    }

    public function enableAsync(bool $flag = true): self
    {
        $this->async = $flag;
        return $this;
    }

    public function send(): string
    {
        $oFlow = Flow::make("HttpV2::send");
        
        $ch = curl_init();
        $headers = [];

        // 构建 URL 和请求体
        if ($this->method === 'get') {
            $url = $this->url . '?' . http_build_query($this->data);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->url);
            $payload = $this->sendJson ? json_encode($this->data) : http_build_query($this->data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            if ($this->method === 'post') {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
            }

            if ($this->sendJson) {
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($payload);
            }
        }

        if ($this->returnJson) {
            $headers[] = 'Accept: application/json';
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // SSL 设置
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 异步发送
        if ($this->async) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 200);
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_exec($ch);
            curl_close($ch);
            return 'sent';
        }

        // 阻塞执行
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Error: {$error}");
        }

        curl_close($ch);
        
        return $response;
//        $this->aResult = $oFlow->sData($response)->done();
//        
//        if ($this->isChain) {
//            return $this;
//        }
//        
//        return $this->aResult;
    }

//    public static function response(int $code, string $message = ''): void
//    {
//        http_response_code($code);
//        echo $message;
//        exit;
//    }
}
