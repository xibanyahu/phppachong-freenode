<?php

declare(strict_types=1);

namespace App\Services\TomTool;

class Http
{
    public $sMethod = 'get';
    public $aData = [];
    public $sToUrl; // 必须
    public $bIsJson = false;

    public function send($sUrl = false): string
    {
        // 两种传递方式
        if ($sUrl) {
            $this->sToUrl = $sUrl;
        }
        
        // 初始化 cURL 会话
        $ch = curl_init();
        
        // 设置请求 URL
        curl_setopt($ch, CURLOPT_URL, $this->sToUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应而不是输出

        // 根据请求方法设置 cURL 选项
        switch (strtolower($this->sMethod)) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true); // 设置为 POST 请求
                if ($this->bIsJson) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->aData)); // 设置 POST 数据为 JSON
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json', // 设置内容类型为 JSON
                        'Content-Length: ' . strlen(json_encode($this->aData)) // 设置内容长度
                    ]);
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->aData)); // 设置 POST 数据为普通表单格式
                }
                break;

            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // 设置为 PUT 请求
                if ($this->bIsJson) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->aData)); // 设置 PUT 数据为 JSON
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json', // 设置内容类型为 JSON
                        'Content-Length: ' . strlen(json_encode($this->aData)) // 设置内容长度
                    ]);
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->aData)); // 设置 PUT 数据为普通表单格式
                }
                break;

            case 'get':
                // 默认使用 GET 请求
                curl_setopt($ch, CURLOPT_HTTPGET, true); // 设置为 GET 请求
                curl_setopt($ch, CURLOPT_URL, $this->sToUrl . '?' . http_build_query($this->aData)); // 设置请求 URL
                if ($this->bIsJson) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Accept: application/json' // 设置接受的内容类型为 JSON
                    ]);
                }
                break;
        }

        // 禁用 SSL 验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证对等证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证主机名

        // 执行 cURL 请求
        $response = curl_exec($ch);

        // 检查是否有错误
        if (curl_errno($ch)) {
            return 'Error: ' . curl_error($ch);
        }

        // 关闭 cURL 会话
        curl_close($ch);

        // 返回响应
        return $response;
    }
    
    public static function response($iCode, $sMsg = '')
    {
        http_response_code($iCode);
        echo $sMsg;
        exit;
    }
}
