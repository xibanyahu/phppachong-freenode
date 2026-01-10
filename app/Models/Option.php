<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'option';
    public $timestamps = false;
    protected $primaryKey = 'k';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public static function _hit($k)
    {
        $v = self::where("k", $k)->first()?->v;
        
        return $v;
    }
    
    public static function _list()
    {
        $oSelf = self::get();
        
        return self::_filterList($oSelf);
    }
    
    public static function _filter($oOption)
    {
        $arr = [];
        $arr[$oOption->k] = $oOption->v;
        
        return $arr;
    }
    
    public static function _filterList($oOption)
    {
        $arr = [];
        foreach ($oOption as $oOptionRow) {
            $arr[] = self::_filter($oOptionRow);
        }
        
        return $arr;
    }
    
}
