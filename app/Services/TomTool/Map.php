<?php

declare(strict_types=1);

namespace App\Services\TomTool;

class Map
{
    public static function fnClientToSfx($sClient)
    {
        $sfx = "";
        
        switch ($sClient) {
            case "clash":
                $sfx = "yaml";
                break;
            case "v2ray":
                $sfx = "txt";
                break;
            default:
                break;
        }
        
        return $sfx;
    }
}
