<?php

namespace App\Services\Tg\Hook\Mod;

use App\Services\Tg\Hook\Dog;
use App\Services\TomTool\Flow;
use App\Services\TomTool\ThrowableHandler;
use App\Services\Cron\FreenodeMerge as CronFreenodeMerge;
use App\Models\FreenodePool as ModelFreenodePool;
use App\Services\TomTool\HttpV2;

class FreenodePool extends Base {

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelFreenodePool());
        $this->oDog->_setDogName("FreenodePool");
        $this->oDog->_setJsonField(["content"]);
//        $this->oDog->_setStrField(["content"]);
        $this->aUpdate = $aUpdate;
    }
    
    public function __call($sName, $aArg)
    {
        return $this->oDog->$sName($aArg[0]);
    }
    
    public function testF()
    {
        $a = [
            "telegram"  => "ttttgggg",
            "freenode_slogan"   => [
                "siteUrl"  => "每天更新免费节点：{{siteUrl}}",
                "telegram"  => "tg频道：{{telegram}}",
            ],
        ];
        
        $s = json_encode($a, JSON_UNESCAPED_UNICODE);
        
        echo $s;
        exit;
    }
    
    public function testC()
    {
        $str = "ss://YWVzLTI1Ni1jZmI6YW1hem9uc2tyMDU@63.180.254.10:443#tg%E9%A2%91%E9%81%93%3A%40ripaojiedian
vmess://eyJhZGQiOiJ2MTAuaGRhY2QuY29tIiwiYWlkIjoiMiIsImFscG4iOiIiLCJob3N0IjoiIiwiaWQiOiJjYmIzZjg3Ny1kMWZiLTM0NGMtODdhOS1kMTUzYmZmZDU0ODQiLCJuZXQiOiJ0Y3AiLCJwYXRoIjoiLyIsInBvcnQiOiIzMDgwNyIsInBzIjoi6aaZ5rivfEByaXBhb2ppZWRpYW4iLCJzY3kiOiJhdXRvIiwic25pIjoiIiwidGxzIjoiIiwidHlwZSI6Im5vbmUiLCJ2IjoiMiJ9
trojan://76d630f2af6619c4a5de0ef953df3c6a@112.118.116.127:443/?type=tcp&security=tls&sni=www.nintendogames.net#%E9%A6%99%E6%B8%AF3%7C%40ripaojiedian
trojan://BxceQaOe@58.152.53.174:443/?type=tcp&security=tls&sni=t.me%2Fripaojiedian&allowInsecure=1#%E9%A6%99%E6%B8%AF4%7C%40ripaojiedian
trojan://BxceQaOe@219.78.209.224:443/?type=tcp&security=tls&sni=t.me%2Fripaojiedian&allowInsecure=1#%E9%A6%99%E6%B8%AF5%7C%40ripaojiedian
trojan://BxceQaOe@3.112.202.47:26378/?type=tcp&security=tls&sni=t.me%2Fripaojiedian&allowInsecure=1#%E6%97%A5%E6%9C%AC%7C%40ripaojiedian
trojan://76d630f2af6619c4a5de0ef953df3c6a@153.121.65.107:3093/?type=tcp&security=tls&sni=www.nintendogames.net#%E6%97%A5%E6%9C%AC2%7C%40ripaojiedian
trojan://76d630f2af6619c4a5de0ef953df3c6a@153.121.53.187:3093/?type=tcp&security=tls&sni=www.nintendogames.net#%E6%97%A5%E6%9C%AC3%7C%40ripaojiedian
trojan://76d630f2af6619c4a5de0ef953df3c6a@160.16.144.208:507/?type=tcp&security=tls&sni=www.nintendogames.net#%E6%96%B0%E5%8A%A0%E5%9D%A1%7C%40ripaojiedian
trojan://76d630f2af6619c4a5de0ef953df3c6a@52.195.224.88:3145/?type=tcp&security=tls&sni=www.nintendogames.net#%E6%96%B0%E5%8A%A0%E5%9D%A12%7C%40ripaojiedian
trojan://BxceQaOe@52.195.5.65:804/?type=tcp&security=tls&sni=t.me%252Fripaojiedian&allowInsecure=1#%E6%96%B0%E5%8A%A0%E5%9D%A13%7C%40ripaojiedian
trojan://76d630f2af6619c4a5de0ef953df3c6a@160.16.236.32:4054/?type=tcp&security=tls&sni=www.nintendogames.net#%E7%BE%8E%E5%9B%BD%7C%40ripaojiedian
trojan://76d630f2af6619c4a5de0ef953df3c6a@153.121.51.29:2890/?type=tcp&security=tls&sni=www.nintendogames.net&allowInsecure=1#%E7%BE%8E%E5%9B%BD2%7C%40ripaojiedian
trojan://BxceQaOe@52.195.5.65:4569/?type=tcp&security=tls&sni=t.me%2Fripaojiedian&allowInsecure=1#%E7%BE%8E%E5%9B%BD3%7C%40ripaojiedian
ss://YWVzLTI1Ni1jZmI6WG44aktkbURNMDBJZU8lIyQjZkpBTXRzRUFFVU9wSC9ZV1l0WXFERm5UMFNW@103.186.155.205:38388#%E8%B6%8A%E5%8D%97%7C%40ripaojiedian
";
        $str = base64_decode($str);
        echo $str;
        exit;
    }
    
    public function testE()
    {
        $s = "ss://YWVzLTI1Ni1jZmI6YW1hem9uc2tyMDVANjMuMTgwLjI1NC4xMDo0NDM=#%F0%9F%87%B8%F0%9F%87%AC%20%E6%96%B0%E5%8A%A0%E5%9D%A1%20SG-17";
        $s = "ss://YWVzLTEyOC1nY206YzAyMzYyZWE5NGJjYWY4ZEBydGdkZnNnZGZzLmZzZHRnYWVydC5zdXJmOjUwMzA1#%F0%9F%87%B8%F0%9F%87%AC%20%E6%96%B0%E5%8A%A0%E5%9D%A1%20SG-17";
        $aTmp = explode("#", $s);
        $sName = $aTmp[1];
        $sConfig = $aTmp[0];
        
        $aTmp = explode("://", $sConfig);
        $sProtocol = $aTmp[0];
        $sConfig = $aTmp[1];
        
        $sConfig = base64_decode($sConfig);
//        $sConfig = "aes-256-cfb:amazonskr05@63.180.254.10:443";
        $sConfig = "aes-128-gcm:amazonskr05@63.180.254.10:443";
        
        $a = [
            "protocol"  => $sProtocol,
            "config"    => $sConfig,
            "name"      => $sName
        ];
        
        $sConfig = base64_encode($sConfig);
        tomd($a);
        $s = $sProtocol."://".$sConfig."#".$sName;
        echo $s;
        exit;
    }
    
    public function testD()
    {
        $s = "ss://YWVzLTEyOC1nY206YzAyMzYyZWE5NGJjYWY4ZEBydGdkZnNnZGZzLmZzZHRnYWVydC5zdXJmOjUwMzA1#%F0%9F%87%B8%F0%9F%87%AC%20%E6%96%B0%E5%8A%A0%E5%9D%A1%20SG-17";
        
//        $
        
        $s = base64_decode($s);
        
        echo $s;
        exit;
    }
    
    public function testB()
    {
        $str = "c3M6Ly9ZV1Z6TFRJMU5pMWpabUk2WVcxaGVtOXVjMnR5TURVQDYzLjE4MC4yNTQuMTA6NDQzIyVFNiVBRiU4RiVFNSVBNCVBOSVFNiU5QiVCNCVFNiU5NiVCMCVFNSU4NSU4RCVFOCVCNCVCOSVFOCU4QSU4MiVFNyU4MiVCOSVFRiVCQyU5QV9hZG1pbi5sb2MrJTNBJTVCcmlwYW9qaWVkaWFuJTVEJTNBCnRyb2phbjovLzc2ZDYzMGYyYWY2NjE5YzRhNWRlMGVmOTUzZGYzYzZhQDE2MC4xNi4yMzYuMzI6NDA1ND9hbGxvd0luc2VjdXJlPTAmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCMlRTclQkUlOEUlRTUlOUIlQkQrMDErJTNBJTVCc3RhaXJub2RlJTVEJTNBCnRyb2phbjovLzc2ZDYzMGYyYWY2NjE5YzRhNWRlMGVmOTUzZGYzYzZhQDE1My4xMjEuNTEuMjk6Mjg5MD9hbGxvd0luc2VjdXJlPTAmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCMlRTclQkUlOEUlRTUlOUIlQkQrMDIrJTNBJTVCc3RhaXJub2RlJTVEJTNBCnRyb2phbjovL0J4Y2VRYU9lQDUyLjE5NS41LjY1OjQ1Njk/YWxsb3dJbnNlY3VyZT0xJnNuaT10Lm1lL3JpcGFvamllZGlhbiMlRTclQkUlOEUlRTUlOUIlQkQrMDMrJTNBJTVCc3RhaXJub2RlJTVEJTNBCmh5c3RlcmlhOi8vNTEuMTU5LjIyNi4xOjQ5MDAzP2FscG49aDMmYXV0aD1kb25ndGFpd2FuZy5jb20mYXV0aF9zdHI9ZG9uZ3RhaXdhbmcuY29tJmRlbGF5PTE1NDcmZG93bm1icHM9NTUmcHJvdG9jb2w9dWRwJmluc2VjdXJlPTEmcGVlcj1hcHBsZS5jb20mdWRwPXRydWUmdXBtYnBzPTExIyVFNyVCRSU4RSVFNSU5QiVCRCswNCslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnZsZXNzOi8vYmU1MjA2MGUtNzkzNC00YmM2LTg5YjMtMGI2Y2Y0Y2RhNzg5QDY5Ljg0LjE4Mi4xMzo0NDM/c2VjdXJpdHk9dGxzJnR5cGU9d3MmcGF0aD0va2JqYy9mcjEmaG9zdD1mci0xLWZyLTEuYWlvcGVuLnNicyZzbmk9ZnItMS1mci0xLmFpb3Blbi5zYnMmZnA9Y2hyb21lIyVFNyVCRSU4RSVFNSU5QiVCRCswNSslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCmh5c3RlcmlhMjovL2JlNTIwNjBlLTc5MzQtNGJjNi04OWIzLTBiNmNmNGNkYTc4OUAxMzQuMTk1LjEwMS4xOTE6MzUwMDA/aW5zZWN1cmU9MSZzbmk9d3d3LmFwcGxlLmNvbSZtcG9ydD0zNTAwMC0zOTAwMCMlRTclQkUlOEUlRTUlOUIlQkQrMDYrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovL2Y4MWQ1YjliLWM1YjctNDZjYi04NzEwLTBjZjQxYWIzMzU3MEBhbnRpbWFnZS5mb25peGFwcC5vcmc6NDQzP3NlY3VyaXR5PXJlYWxpdHkmZW5jcnlwdGlvbj1ub25lJnBiaz1XZU5sZl9oUHNjQkxTd3RnMVBnT211cksxZjdWeVFTTlBtT1llRzB3SVF3JmhlYWRlclR5cGU9bm9uZSZmcD1jaHJvbWUmc3B4PS8mdHlwZT10Y3AmZmxvdz14dGxzLXJwcngtdmlzaW9uJnNuaT1hamF4LmNsb3VkZmxhcmUuY29tJnNpZD02NTkwY2IjJUU3JUJFJThFJUU1JTlCJUJEKzA3KyUzQSU1QnYycmF5c2hhcmUlNUQlM0EKdmxlc3M6Ly9lM2FmNDliYS02YTQxLTRmYTEtOGYyMS1mNDBkMWYxNTRjNjhAbGlmZXN0ZWFsZXIuZm9uaXhhcHAub3JnOjQ0Mz9zZWN1cml0eT1yZWFsaXR5JmVuY3J5cHRpb249bm9uZSZwYms9REoxbDFiZ3Z3cGotU29IdUM2ZUxWZnpkM3JwMXpRbUNWNzZmZFZhek1nYyZoZWFkZXJUeXBlPW5vbmUmZnA9Y2hyb21lJnR5cGU9dGNwJmZsb3c9eHRscy1ycHJ4LXZpc2lvbiZzbmk9Y29uZmlndXJhdGlvbi5hcHBsZS5jb20mc2lkPTE3M2M1OCMlRTclQkUlOEUlRTUlOUIlQkQrMDgrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp0cm9qYW46Ly83NmQ2MzBmMmFmNjYxOWM0YTVkZTBlZjk1M2RmM2M2YUAxNjAuMTYuMjM2LjMyOjQwNTQvP3R5cGU9dGNwJnNlY3VyaXR5PXRscyZzbmk9d3d3Lm5pbnRlbmRvZ2FtZXMubmV0IyVFNyVCRSU4RSVFNSU5QiVCRCswOSslM0ElNUJyaXBhb2ppZWRpYW4lNUQlM0EKdHJvamFuOi8vNzZkNjMwZjJhZjY2MTljNGE1ZGUwZWY5NTNkZjNjNmFAMTUzLjEyMS41MS4yOToyODkwLz90eXBlPXRjcCZzZWN1cml0eT10bHMmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCZhbGxvd0luc2VjdXJlPTEjJUU3JUJFJThFJUU1JTlCJUJEKzEwKyUzQSU1QnJpcGFvamllZGlhbiU1RCUzQQp0cm9qYW46Ly9CeGNlUWFPZUA1Mi4xOTUuNS42NTo0NTY5Lz90eXBlPXRjcCZzZWN1cml0eT10bHMmc25pPXQubWUvcmlwYW9qaWVkaWFuJmFsbG93SW5zZWN1cmU9MSMlRTclQkUlOEUlRTUlOUIlQkQrMTErJTNBJTVCcmlwYW9qaWVkaWFuJTVEJTNBCnRyb2phbjovL0J4Y2VRYU9lQDMuMTEyLjIwMi40NzoyNjM3OD9hbGxvd0luc2VjdXJlPTEmc25pPXQubWUvcmlwYW9qaWVkaWFuIyVFNiU5NyVBNSVFNiU5QyVBQyswMSslM0ElNUJzdGFpcm5vZGUlNUQlM0EKdHJvamFuOi8vNzZkNjMwZjJhZjY2MTljNGE1ZGUwZWY5NTNkZjNjNmFAMTUzLjEyMS42NS4xMDc6MzA5Mz9hbGxvd0luc2VjdXJlPTAmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCMlRTYlOTclQTUlRTYlOUMlQUMrMDIrJTNBJTVCc3RhaXJub2RlJTVEJTNBCnRyb2phbjovLzc2ZDYzMGYyYWY2NjE5YzRhNWRlMGVmOTUzZGYzYzZhQDE1My4xMjEuNTMuMTg3OjMwOTM/YWxsb3dJbnNlY3VyZT0wJnNuaT13d3cubmludGVuZG9nYW1lcy5uZXQjJUU2JTk3JUE1JUU2JTlDJUFDKzAzKyUzQSU1QnN0YWlybm9kZSU1RCUzQQp0cm9qYW46Ly9CeGNlUWFPZUAzLjExMi4yMDIuNDc6MjYzNzgvP3R5cGU9dGNwJnNlY3VyaXR5PXRscyZzbmk9dC5tZS9yaXBhb2ppZWRpYW4mYWxsb3dJbnNlY3VyZT0xIyVFNiU5NyVBNSVFNiU5QyVBQyswNCslM0ElNUJyaXBhb2ppZWRpYW4lNUQlM0EKdHJvamFuOi8vNzZkNjMwZjJhZjY2MTljNGE1ZGUwZWY5NTNkZjNjNmFAMTUzLjEyMS42NS4xMDc6MzA5My8/dHlwZT10Y3Amc2VjdXJpdHk9dGxzJnNuaT13d3cubmludGVuZG9nYW1lcy5uZXQjJUU2JTk3JUE1JUU2JTlDJUFDKzA1KyUzQSU1QnJpcGFvamllZGlhbiU1RCUzQQp0cm9qYW46Ly83NmQ2MzBmMmFmNjYxOWM0YTVkZTBlZjk1M2RmM2M2YUAxNTMuMTIxLjUzLjE4NzozMDkzLz90eXBlPXRjcCZzZWN1cml0eT10bHMmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCMlRTYlOTclQTUlRTYlOUMlQUMrMDYrJTNBJTVCcmlwYW9qaWVkaWFuJTVEJTNBCnZtZXNzOi8veyJ2IjoiMiIsInBzIjoiIiwiYWRkIjoidjEwLmhkYWNkLmNvbSIsInBvcnQiOiIzMDgwNyIsInR5cGUiOiJub25lIiwiaWQiOiJjYmIzZjg3Ny1kMWZiLTM0NGMtODdhOS1kMTUzYmZmZDU0ODQiLCJhaWQiOiJcdTAwMDIiLCJuZXQiOiJ0Y3AiLCJwYXRoIjoiXC8iLCJob3N0IjoidjEwLmhkYWNkLmNvbSIsInRscyI6IiJ9IyVFOSVBNiU5OSVFNiVCOCVBRiswMSslM0ElNUJzdGFpcm5vZGUlNUQlM0EKdHJvamFuOi8vNzZkNjMwZjJhZjY2MTljNGE1ZGUwZWY5NTNkZjNjNmFAMTEyLjExOC4xMTYuMTI3OjQ0Mz9hbGxvd0luc2VjdXJlPTAmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCMlRTklQTYlOTklRTYlQjglQUYrMDIrJTNBJTVCc3RhaXJub2RlJTVEJTNBCnRyb2phbjovL0J4Y2VRYU9lQDU4LjE1Mi41My4xNzQ6NDQzP2FsbG93SW5zZWN1cmU9MSZzbmk9dC5tZS9yaXBhb2ppZWRpYW4jJUU5JUE2JTk5JUU2JUI4JUFGKzAzKyUzQSU1QnN0YWlybm9kZSU1RCUzQQp0cm9qYW46Ly9CeGNlUWFPZUAyMTkuNzguMjA5LjIyNDo0NDM/YWxsb3dJbnNlY3VyZT0xJnNuaT10Lm1lL3JpcGFvamllZGlhbiMlRTklQTYlOTklRTYlQjglQUYrMDQrJTNBJTVCc3RhaXJub2RlJTVEJTNBCnZtZXNzOi8veyJhZGQiOiJ2MTAuaGRhY2QuY29tIiwiYWlkIjoiMiIsImFscG4iOiIiLCJob3N0IjoiIiwiaWQiOiJjYmIzZjg3Ny1kMWZiLTM0NGMtODdhOS1kMTUzYmZmZDU0ODQiLCJuZXQiOiJ0Y3AiLCJwYXRoIjoiXC8iLCJwb3J0IjoiMzA4MDciLCJwcyI6IiIsInNjeSI6ImF1dG8iLCJzbmkiOiIiLCJ0bHMiOiIiLCJ0eXBlIjoibm9uZSIsInYiOiIyIn0jJUU5JUE2JTk5JUU2JUI4JUFGKzA1KyUzQSU1QnJpcGFvamllZGlhbiU1RCUzQQp0cm9qYW46Ly83NmQ2MzBmMmFmNjYxOWM0YTVkZTBlZjk1M2RmM2M2YUAxMTIuMTE4LjExNi4xMjc6NDQzLz90eXBlPXRjcCZzZWN1cml0eT10bHMmc25pPXd3dy5uaW50ZW5kb2dhbWVzLm5ldCMlRTklQTYlOTklRTYlQjglQUYrMDYrJTNBJTVCcmlwYW9qaWVkaWFuJTVEJTNBCnRyb2phbjovL0J4Y2VRYU9lQDU4LjE1Mi41My4xNzQ6NDQzLz90eXBlPXRjcCZzZWN1cml0eT10bHMmc25pPXQubWUvcmlwYW9qaWVkaWFuJmFsbG93SW5zZWN1cmU9MSMlRTklQTYlOTklRTYlQjglQUYrMDcrJTNBJTVCcmlwYW9qaWVkaWFuJTVEJTNBCnRyb2phbjovL0J4Y2VRYU9lQDIxOS43OC4yMDkuMjI0OjQ0My8/dHlwZT10Y3Amc2VjdXJpdHk9dGxzJnNuaT10Lm1lL3JpcGFvamllZGlhbiZhbGxvd0luc2VjdXJlPTEjJUU5JUE2JTk5JUU2JUI4JUFGKzA4KyUzQSU1QnJpcGFvamllZGlhbiU1RCUzQQp0cm9qYW46Ly83NmQ2MzBmMmFmNjYxOWM0YTVkZTBlZjk1M2RmM2M2YUAxNjAuMTYuMTQ0LjIwODo1MDc/YWxsb3dJbnNlY3VyZT0wJnNuaT13d3cubmludGVuZG9nYW1lcy5uZXQjJUU2JTk2JUIwJUU1JThBJUEwJUU1JTlEJUExKzAxKyUzQSU1QnN0YWlybm9kZSU1RCUzQQp0cm9qYW46Ly83NmQ2MzBmMmFmNjYxOWM0YTVkZTBlZjk1M2RmM2M2YUA1Mi4xOTUuMjI0Ljg4OjMxNDU/YWxsb3dJbnNlY3VyZT0wJnNuaT13d3cubmludGVuZG9nYW1lcy5uZXQjJUU2JTk2JUIwJUU1JThBJUEwJUU1JTlEJUExKzAyKyUzQSU1QnN0YWlybm9kZSU1RCUzQQp0cm9qYW46Ly9CeGNlUWFPZUA1Mi4xOTUuNS42NTo4MDQ/YWxsb3dJbnNlY3VyZT0xJnNuaT10Lm1lJTJGcmlwYW9qaWVkaWFuIyVFNiU5NiVCMCVFNSU4QSVBMCVFNSU5RCVBMSswMyslM0ElNUJzdGFpcm5vZGUlNUQlM0EKdmxlc3M6Ly9mZjgzMTk4OC1iNjRhLTRiYzMtOTNkZC01ODM4OWM5MWEwM2FAc2cuZmx1eGNvbW11bml0eS5teS5pZDoyMDgyPyZzZWN1cml0eT1mYWxzZSZmcD1jaHJvbWUmdHlwZT13cyZoZWFkZXJUeXBlPW5vbmUmaG9zdD1zZy5mbHV4Y29tbXVuaXR5Lm15LmlkJnBhdGg9L3ZsZXNzIyVFNiU5NiVCMCVFNSU4QSVBMCVFNSU5RCVBMSswNCslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnRyb2phbjovLzc2ZDYzMGYyYWY2NjE5YzRhNWRlMGVmOTUzZGYzYzZhQDE2MC4xNi4xNDQuMjA4OjUwNy8/dHlwZT10Y3Amc2VjdXJpdHk9dGxzJnNuaT13d3cubmludGVuZG9nYW1lcy5uZXQjJUU2JTk2JUIwJUU1JThBJUEwJUU1JTlEJUExKzA1KyUzQSU1QnJpcGFvamllZGlhbiU1RCUzQQp0cm9qYW46Ly83NmQ2MzBmMmFmNjYxOWM0YTVkZTBlZjk1M2RmM2M2YUA1Mi4xOTUuMjI0Ljg4OjMxNDUvP3R5cGU9dGNwJnNlY3VyaXR5PXRscyZzbmk9d3d3Lm5pbnRlbmRvZ2FtZXMubmV0IyVFNiU5NiVCMCVFNSU4QSVBMCVFNSU5RCVBMSswNislM0ElNUJyaXBhb2ppZWRpYW4lNUQlM0EKdHJvamFuOi8vQnhjZVFhT2VANTIuMTk1LjUuNjU6ODA0Lz90eXBlPXRjcCZzZWN1cml0eT10bHMmc25pPXQubWUlMkZyaXBhb2ppZWRpYW4mYWxsb3dJbnNlY3VyZT0xIyVFNiU5NiVCMCVFNSU4QSVBMCVFNSU5RCVBMSswNyslM0ElNUJyaXBhb2ppZWRpYW4lNUQlM0EKc3M6Ly9ZV1Z6TFRJMU5pMWpabUk2V0c0NGFrdGtiVVJOTURCSlpVOGxJeVFqWmtwQlRYUnpSVUZGVlU5d1NDOVpWMWwwV1hGRVJtNVVNRk5XQDEwMy4xODYuMTU1LjIwNTozODM4OCMlRTglQjYlOEElRTUlOEQlOTcrMDErJTNBJTVCcmlwYW9qaWVkaWFuJTVEJTNBCmh5c3RlcmlhOi8vMTk1LjE1NC4yMDAuNDA6MTUwMTA/YWxwbj1oMyZhdXRoPWRvbmd0YWl3YW5nLmNvbSZhdXRoX3N0cj1kb25ndGFpd2FuZy5jb20mZG93bm1icHM9MTAwJnByb3RvY29sPXVkcCZpbnNlY3VyZT0xJnBlZXI9YXBwbGUuY29tJnVkcD10cnVlJnVwbWJwcz0xMDAjJUU2JUIzJTk1JUU1JTlCJUJEKzAxKyUzQSU1QnYycmF5c2hhcmUlNUQlM0EKaHlzdGVyaWE6Ly82Mi4yMTAuMTAuMTg2OjUwMDExP2FscG49aDMmYXV0aD1kb25ndGFpd2FuZy5jb20mZG93bm1icHM9NTUgTWJwcyZwcm90b2NvbD11ZHAmaW5zZWN1cmU9MSZwZWVyPWFwcGxlLmNvbSZ1cG1icHM9MTEgTWJwcyMlRTYlQjMlOTUlRTUlOUIlQkQrMDIrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQpoeXN0ZXJpYTovLzYyLjIxMC4xMC4xODY6MzU1MTE/YWxwbj1oMyZhdXRoPWRvbmd0YWl3YW5nLmNvbSZhdXRoX3N0cj1kb25ndGFpd2FuZy5jb20mZG93bm1icHM9NTUmcHJvdG9jb2w9dWRwJmluc2VjdXJlPTEmcGVlcj1hcHBsZS5jb20mdWRwPXRydWUmdXBtYnBzPTExIyVFNiVCMyU5NSVFNSU5QiVCRCswMyslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCmh5c3RlcmlhMjovL2JkNTE1NTc0LTNiMTAtMTFlZS1hYzE0LWYyM2M5MTY0Y2E1ZEBmODcwM2QxYS10NnI1czAtdGIwMTZiLTFtN2w0LmhrMi5oeWh1YXdlaS5jb206NDQzP3NuaT1mODcwM2QxYS10NnI1czAtdGIwMTZiLTFtN2w0LmhrMi5oeWh1YXdlaS5jb20mbXBvcnQ9MzAwMDAtNjAwMDAjJUU0JUI4JUFEJUU1JTlCJUJEKzAxKyUzQSU1QnYycmF5c2hhcmUlNUQlM0EKaHlzdGVyaWEyOi8vMGRlMzdjZGMtYWJmZi0xMWVmLWI3YzYtZjIzYzkxM2M4ZDJiQGRmOWFkNGRhLXQ2dDBnMC10ODBvcWQtMXJzdXcuaGsyLmh5aHVhd2VpLmNvbTo0NDM/c25pPWRmOWFkNGRhLXQ2dDBnMC10ODBvcWQtMXJzdXcuaGsyLmh5aHVhd2VpLmNvbSZtcG9ydD0zMDAwMC02MDAwMCMlRTQlQjglQUQlRTUlOUIlQkQrMDIrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQpoeXN0ZXJpYTI6Ly85ZTFkZGMwNC0wNzNiLTExZWQtYmQ3Yy1mMjNjOTEzYzhkMmJAMjJkNmY4MmYtdDZ0MGcwLXRrZ2s0dy0xZnZkaC5oazIuaHlodWF3ZWkuY29tOjQ0Mz9zbmk9MjJkNmY4MmYtdDZ0MGcwLXRrZ2s0dy0xZnZkaC5oazIuaHlodWF3ZWkuY29tJm1wb3J0PTMwMDAwLTYwMDAwIyVFNCVCOCVBRCVFNSU5QiVCRCswMyslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCmh5c3RlcmlhMjovLzM0ZjBjZjQyLTBmOGEtMTFlYy1hOGJmLWYyM2M5MWNmYmJjOUAyMWQ5NDIyMS10NnBiNDAtdGRwczlsLTE1NWQ5LmhrMi5oeWh1YXdlaS5jb206NDQzP3NuaT0yMWQ5NDIyMS10NnBiNDAtdGRwczlsLTE1NWQ5LmhrMi5oeWh1YXdlaS5jb20mbXBvcnQ9MzAwMDAtNjAwMDAjJUU0JUI4JUFEJUU1JTlCJUJEKzA0KyUzQSU1QnYycmF5c2hhcmUlNUQlM0EKaHlzdGVyaWEyOi8vYWFmZWRjNjQtOWMyMy0xMWVmLWE3OWYtZjIzYzkxY2ZiYmM5QDY3NjFlMmFkLXQ2cGI0MC10bmZlMnotMXM0aGMuaGsyLmh5aHVhd2VpLmNvbTo0NDM/c25pPTY3NjFlMmFkLXQ2cGI0MC10bmZlMnotMXM0aGMuaGsyLmh5aHVhd2VpLmNvbSZtcG9ydD0zMDAwMC02MDAwMCMlRTQlQjglQUQlRTUlOUIlQkQrMDUrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQpoeXN0ZXJpYTI6Ly84OWIwZWE4Yy04MDZiLTExZWQtOTRiYS1mMjNjOTEzYzhkMmJAYTcyYjRlOWItdDZ0MGcwLXQ3YzA1by1uMDlmLmhrMi5oeWh1YXdlaS5jb206NDQzP3NuaT1hNzJiNGU5Yi10NnQwZzAtdDdjMDVvLW4wOWYuaGsyLmh5aHVhd2VpLmNvbSZtcG9ydD0zMDAwMC02MDAwMCMlRTQlQjglQUQlRTUlOUIlQkQrMDYrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQpoeXN0ZXJpYTI6Ly85NmM3OThmNi00NjQ1LTExZWUtYjhhMS1mMjNjOTE2NGNhNWRAMTFjNzZlZjAtdDZyNXMwLXRoOWt4OS0xb3M0My5oazIuaHlodWF3ZWkuY29tOjQ0Mz9zbmk9MTFjNzZlZjAtdDZyNXMwLXRoOWt4OS0xb3M0My5oazIuaHlodWF3ZWkuY29tJm1wb3J0PTMwMDAwLTYwMDAwIyVFNCVCOCVBRCVFNSU5QiVCRCswNyslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCmh5c3RlcmlhMjovL2QyZjBiZWUyLTAyZjEtMTFmMC04ZWIwLWYyM2M5MTY0Y2E1ZEBkMTViMGEyZi10NnBiNDAtdGM2MWNzLTF0amE4LmhrMi5oeWh1YXdlaS5jb206NDQzP3NuaT1kMTViMGEyZi10NnBiNDAtdGM2MWNzLTF0amE4LmhrMi5oeWh1YXdlaS5jb20mbXBvcnQ9MzAwMDAtNjAwMDAjJUU0JUI4JUFEJUU1JTlCJUJEKzA4KyUzQSU1QnYycmF5c2hhcmUlNUQlM0EKaHlzdGVyaWEyOi8vY2U0ODFjNmUtZjRjOC0xMWVmLWJiYjAtZjIzYzkxY2ZiYmM5QDg0MDQxYjlmLXQ2cGI0MC10NnowZXQtMXQ2MG8uaGsyLmh5aHVhd2VpLmNvbTo0NDM/c25pPTg0MDQxYjlmLXQ2cGI0MC10NnowZXQtMXQ2MG8uaGsyLmh5aHVhd2VpLmNvbSZtcG9ydD0zMDAwMC02MDAwMCMlRTQlQjglQUQlRTUlOUIlQkQrMDkrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovLzA5NGU2NmE5LThkOTItNDFmZS04NWFkLThlN2JmOWI1ZmNhYUBubC03OHctYy5zb3V0aG5ldHdvcmtzLmNsb3VkOjgwPyZ0eXBlPXdzJmhlYWRlclR5cGU9bm9uZSZob3N0PW5sLTc4dy1jLnNvdXRobmV0d29ya3MuY2xvdWQmcGF0aD0vZ2V0V29ya2VyVXBkYXRlcyMlRTQlQkYlODQlRTclQkQlOTclRTYlOTYlQUYrMDErJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovLzJhMmE2MDYyLTAwM2MtMDAwNC1iMjkzLWVlMGQwNDE2YWZlZUAxNzYuMTIzLjE2Mi4yNTM6ODQ0Mz9zZWN1cml0eT1yZWFsaXR5JnR5cGU9dGNwJnNuaT1waW1nLm15Y2RuLm1lJmZwPWNocm9tZSZmbG93PXh0bHMtcnByeC12aXNpb24mc2lkPWFlYTEwNGU3MzAmcGJrPTczWGdsUjlsT2FYQklTZDdtR2dURVA2MHYyOG16OHBtQ0RpdmNEQUZfV1UmZW5jcnlwdGlvbj1ub25lIyVFNCVCRiU4NCVFNyVCRCU5NyVFNiU5NiVBRiswMislM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnZsZXNzOi8vY2I3MTg4Y2YtMmMzYS00M2E1LWFmOTAtZDg5MzkyMmU0OTE3QGlzdGFuYnVsLmZvbml4YXBwLm9yZzo0NDM/c2VjdXJpdHk9cmVhbGl0eSZlbmNyeXB0aW9uPW5vbmUmcGJrPWVTME5xSkNBbzZKbjRQdVUzblU0QTVEN1J5TmJuM1d5aGlwWFZpZGRuekkmaGVhZGVyVHlwZT1ub25lJmZwPWNocm9tZSZzcHg9LyZ0eXBlPXRjcCZmbG93PXh0bHMtcnByeC12aXNpb24mc25pPWZvbnRzLmdzdGF0aWMuY29tJnNpZD01NDU3IyVFNCVCRiU4NCVFNyVCRCU5NyVFNiU5NiVBRiswMyslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnRyb2phbjovL2FmNGIwNjRhOWMzOTQ3MmZiMzRhZDhiMTEzYWNkYzZiQDQ2LjI5LjE2MS4yMzc6NDQzP3NuaT00Ni4yOS4xNjEuMjM3JmFsbG93SW5zZWN1cmU9MSMlRTQlQkYlODQlRTclQkQlOTclRTYlOTYlQUYrMDQrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovLzJhMmE2MDYyLTAwM2MtNGY0NC1iMjkzLWVlMGQwNDE2YWZlZUA5MS44NC4xMTguMTMwOjE0NDM/c2VjdXJpdHk9cmVhbGl0eSZ0eXBlPWdycGMmbW9kZT1ndW4mc2VydmljZU5hbWU9YWRtaW4mc25pPW96b24ucnUmZnA9Y2hyb21lJnNpZD00N2U5MmE0YjA1MTNiMjdmJnBiaz1KdURyVVpzZ0h0MGlfaHoza09oUWJpWDcwOHZialI5dUhIRndpM2ZXcVNjJmVuY3J5cHRpb249bm9uZSMlRTglOEIlQjElRTUlOUIlQkQrMDErJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovLzRhNmRlMTgzLTJhZDMtNDU2Yy1iNDIwLWZlZWFjODFmNjI1N0A1MS4yNTAuMjUuMTE6MTQ4OD9zZWN1cml0eT1yZWFsaXR5JmVuY3J5cHRpb249bm9uZSZwYms9U2JWS09FTWpLMHNJbGJ3ZzRha3lCZzVtTDVLWnd3Qi1lZDRlRUU3WW5SYyZoZWFkZXJUeXBlPW5vbmUmZnA9Y2hyb21lJnR5cGU9dGNwJmZsb3c9eHRscy1ycHJ4LXZpc2lvbiZzbmk9YWRzLng1LnJ1JnNpZD02YmE4NTE3OWUzMGQ0ZmMyIyVFOCU4QiVCMSVFNSU5QiVCRCswMislM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnZsZXNzOi8vNGE2ZGUxODMtMmFkMy00NTZjLWI0MjAtZmVlYWM4MWY2MjU3QDUxLjI1MC4xMDkuNDc6MTQ4OD9zZWN1cml0eT1yZWFsaXR5JmVuY3J5cHRpb249bm9uZSZwYms9U2JWS09FTWpLMHNJbGJ3ZzRha3lCZzVtTDVLWnd3Qi1lZDRlRUU3WW5SYyZoZWFkZXJUeXBlPW5vbmUmZnA9Y2hyb21lJnR5cGU9dGNwJmZsb3c9eHRscy1ycHJ4LXZpc2lvbiZzbmk9YWRzLng1LnJ1JnNpZD02YmE4NTE3OWUzMGQ0ZmMyIyVFOCU4QiVCMSVFNSU5QiVCRCswMyslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnZsZXNzOi8vNGE2ZGUxODMtMmFkMy00NTZjLWI0MjAtZmVlYWM4MWY2MjU3QDUxLjI1MC45OS4xOTA6MTQ4OD9zZWN1cml0eT1yZWFsaXR5JmVuY3J5cHRpb249bm9uZSZwYms9U2JWS09FTWpLMHNJbGJ3ZzRha3lCZzVtTDVLWnd3Qi1lZDRlRUU3WW5SYyZoZWFkZXJUeXBlPW5vbmUmZnA9Y2hyb21lJnR5cGU9dGNwJmZsb3c9eHRscy1ycHJ4LXZpc2lvbiZzbmk9YWRzLng1LnJ1IyVFOCU4QiVCMSVFNSU5QiVCRCswNCslM0ElNUJ2MnJheXNoYXJlJTVEJTNBCnZsZXNzOi8vMmEyYTYwNjItMDAzYy00ZjQ0LWIyOTMtZWUwZDA0MTZhZmVlQDE0NC4xMjQuMjU0LjMxOjE0NDM/c2VjdXJpdHk9cmVhbGl0eSZ0eXBlPWdycGMmbW9kZT1ndW4mc2VydmljZU5hbWU9YWRtaW4mc25pPW96b24ucnUmZnA9Y2hyb21lJnNpZD00N2U5MmE0YjA1MTNiMjdmJnBiaz1KdURyVVpzZ0h0MGlfaHoza09oUWJpWDcwOHZialI5dUhIRndpM2ZXcVNjJmVuY3J5cHRpb249bm9uZSMlRTglOEIlQjElRTUlOUIlQkQrMDUrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovLzkzN2Y4OTQwLWFkOGEtNDA5Zi05M2MzLTRkNjI0OWFmNjI4ZkB1azEub29tei5ydTo0NDM/c2VjdXJpdHk9cmVhbGl0eSZlbmNyeXB0aW9uPW5vbmUmcGJrPV9kc091X3Z4RVU3cFkzc0llR0p4SXBSMEFjMktvcUl5Qm9yMkJ4MEN3R0EmaGVhZGVyVHlwZT1ub25lJmZwPXFxJnR5cGU9dGNwJmZsb3c9eHRscy1ycHJ4LXZpc2lvbiZzbmk9d3d3LmJwLmNvbSZzaWQ9ZDkyMyMlRTglOEIlQjElRTUlOUIlQkQrMDYrJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovLzU1OTc1NDE1LTQzYzAtNDYxYy04NzUyLWQ1YjgwMTMzNjRlM0AxNjcuMTcuNzAuMTY6ODQ0Mz9zZWN1cml0eT1yZWFsaXR5JnR5cGU9dGNwJnNuaT13d3cubWljcm9zb2Z0LmNvbSZmcD1jaHJvbWUmZmxvdz14dGxzLXJwcngtdmlzaW9uJnNpZD04NGY3MGYxMTc3OTgxNTYzJnBiaz04VzlyMmFYR2hLTnR2UHhuTDZFaVBwUXVTbTlPSlQxN3gyeHl4d3hJbzMwJmVuY3J5cHRpb249bm9uZSMlRTUlOEElQTAlRTYlOEIlQkYlRTUlQTQlQTcrMDErJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQp2bGVzczovL2UwM2I1MzhhLTkxYzktNGQzYy1hMmNiLThhNmIxZDhkZjU0ZkAyMTMuMTY1LjYzLjIzNDo2MjcyOD9zZWN1cml0eT1yZWFsaXR5JnR5cGU9dGNwJnNuaT15YW5kZXgucnUmZnA9Y2hyb21lJnNpZD1kOGVkZmFkYjM1ZWNkZjAwJnBiaz1JVGRlX0t0bjdTbEhMeWttUTR2elZYTFhaZDBKN1p4dVMzT2M0Yk5IRmc4JmVuY3J5cHRpb249bm9uZSMlRTYlQjIlOTklRTclODklQjklRTklOTglQkYlRTYlOEIlODklRTQlQkMlQUYrMDErJTNBJTVCdjJyYXlzaGFyZSU1RCUzQQ==";
        
        echo base64_decode($str);
        exit;
    }
    
    public function testA()
    {
        $aData = [
            "siteCode"  => "cfn",
            "client"    => "v2ray",
//            "date"      => "2025-12-12",
        ];
        
//        $sUrl = "https://dd.loc/api/fn/merge/get";
        $sUrl = "https://dd.loc/sub/cfn/v2ray";
        
        $r = HttpV2::make($sUrl, "get")->setDataA($aData)->enableJsonAs()->send();
        
        tomd($r);

        
//        HttpV2::make($url)->aData($aData)->setJsonAs()->setAsync()->setMethod("get")->send();
    }
    
    public function showPool($aParam)
    {
        $sDate = $aParam["aArg"][1] ?? date("Y-m-d");
        $sVtype = $aParam["aOption"][1] ?? "custom";
        
        $sContentType = "content_".$sVtype;
        
        $aPool = ModelFreenodePool::where("date", $sDate)->first()->toArray();
        
        $sContent = $aPool[$sContentType];
        
        $aContent = json_decode($sContent, true);
        
        return $this->toJson($aContent);
    }
    
    public function show($aParam)
    {
        $sSiteCode = $aParam["aOption"][1] ?? "cfn";
        $sClient = $aParam["aOption"][2] ?? "v2ray";
        $sDate = $aParam["aArg"][1] ?? date("Y-m-d");
        
        
        if ($sDate == "?") {
            return "show__[siteCode]__[client] [Y-m-d]";
        }
        
        $sDirBase = $_ENV["dir_base"];
        
        $sFilePath = $sDirBase."public/freenode/merge/{$sSiteCode}/{$sClient}/{$sDate}.txt";
        
        $sContent = file_get_contents($sFilePath);

        $sFeed = "";
        if ($sClient == "v2ray") {
            $sFeed = CronFreenodeMerge::v2rayFeedToStr($sContent);
//            $sContent = base64_decode($sContent);
//            $aContent = explode("\n", $sContent);
//            foreach ($aContent as $sNode) {
//                $aTmp = explode("#", $sNode);
//                $sName = $aTmp[1];
//                $sName = urldecode($sName);
//                $sNode = $aTmp[0]."#".$sName;
//                $sFeed .= $sNode."\n";
//            }
        } else if ($sClient == "clash") {
            $sFeed = $sContent;
        }
        
        return $sFeed;
    }
    
}
