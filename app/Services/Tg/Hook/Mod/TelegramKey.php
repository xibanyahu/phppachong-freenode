<?php

namespace App\Services\Tg\Hook\Mod;

use App\Models\TelegramKey as ModelTelegramKey;
//use App\Models\ArticleCache as ModelArticleCache;
//use App\Models\ArticleCacheTime as ModelArticleCacheTime;
use App\Services\Tg\Hook\Dog;
//use App\Services\Article as ServiceArticle;
//use App\Services\TomTool\Flow;
//use App\Services\HttpQueue;
//use App\Services\TomTool\TelegramVdb;

class TelegramKey extends Base {
    
    private $oDog;

    public function __construct($aUpdate)
    {
        $this->oDog = new Dog(new ModelTelegramKey());
        $this->oDog->_setDogName("telegram_key"); // 必须，me用，正常就是类名，只不过后期没准改名，所以就这里写死，要在最前面
    }
    
    public function __call($sName, $aArg)
    {
        
        return $this->oDog->$sName($aArg[0]);
        
    }
    
}
