<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoveSite extends Model
{
    protected $table = 'dove_site';
    public $timestamps = false;
    
    public static function _getMap()
    {
        $oSelf = self::all();
        
        $aMap = [];
        foreach ($oSelf as $oSelfRow) {
            $aMap[$oSelfRow["code"]] = $oSelfRow["www"].$oSelfRow["api_path"];
        }
        
        return $aMap;
    }
    
}
