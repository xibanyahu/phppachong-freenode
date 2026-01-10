<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DogOption extends Model
{
    protected $table = 'dog_option';
    public $timestamps = false;
    
    public static function _hit($sDogName, $k)
    {
        $v = self::where("dog_name", $sDogName)->where("k", $k)->first()?->v;
//        tomd($sDogName);
        return $v;
    }
    
    public static function _list($sDogName)
    {
        $oSelf = self::where("dog_name", $sDogName)->get();
        
        return self::_filterList($oSelf);
    }
    
//    public static function _limit($sDogName, $iSkip, $iTake)
//    {
//        $oSelf = self::where("dog_name", $sDogName)->skip($iSkip)->take($iTake)->get();
//        
//        return self::__filterList($oSelf);
//    }
    
    public static function _filter($oOption)
    {
        $arr = [];
        $arr[$oOption->dog_name."#".$oOption->k] = $oOption->v;
        
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
