<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlFreenode extends Model
{
    protected $table = 'crawl_freenode';
    public $timestamps = false;
    
//    public static function _listKey()
//    {
//        $aSelf = self::all()->toArray();
//        
//        foreach ($aSelf as &$aSelfRow) {
//            unset($aSelfRow["content"]);
//        }
//        
//        return $aSelf;
//    }
}
