<?php

declare(strict_types=1);

namespace App\Services\TomTool;

class Tom
{
    public static function toJson($aData)
    {
        return json_encode($aData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    public static function toJsonBr($aData)
    {
        $sData = self::toJson($aData);
        $sData = str_replace("\\n", "<br>", $sData);
        
        return $sData;
    }
}
