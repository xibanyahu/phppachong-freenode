<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Article\ListController as ArticleListController;
use App\Http\Controllers\Article\SingleController as ArticleSingleController;
use App\Http\Controllers\ExecController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\HtmlController;
use App\Http\Controllers\SakaiController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\CmdController;
use App\Http\Controllers\Api\ShipController;
//use App\Http\Controllers\Api\GithubController;
use App\Http\Controllers\Api\Tg\HookController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\GoodController;
use App\Http\Controllers\Api\GithubController;
use App\Services\OpenApi\Telegram as ServiceOpenApiTelegram;
use App\Services\Article\Schema as ServiceArticleSchema;
use App\Http\Controllers\Api\FreenodeController as ApiFreenodeController;

Route::any('/api/telegram/post', [ServiceOpenApiTelegram::class, 'post']);
Route::any('/api/article/schema/build', [ServiceArticleSchema::class, 'api_buildArticle']);
Route::any('/api/article/schema/build/receive', [ServiceArticleSchema::class, 'api_build_receive']);
Route::get('/sub/{siteCode}/{client}/{date?}', [ApiFreenodeController::class, "sub"]);

Route::get('/cmd', [CmdController::class, 'index']);
Route::post('/cmd/send', [CmdController::class, 'ajaxSend']);
Route::get('/cmd/image/upload', [CmdController::class, 'imageUpload']);
Route::post('/cmd/image/upload/exec', [CmdController::class, 'imageUploadExec']);

Route::post('/api/open/telegram/queue/add', [ServiceTelegram::class, 'queueAdd']);
Route::post('/api/github/get', [GithubController::class, 'get']);
Route::get('/api/good/rand/{class}', [GoodController::class, 'rand']);
Route::get('/api/freenode/country/rand/{case?}', [CountryController::class, 'rand']);
Route::get('/api/ship/get/{shipCode}/{clientClass}/{clientCode?}', [ShipController::class, 'getShipByCode']);

Route::get('/cron', [CronController::class, 'index']);

Route::any('/api/tg/hook', [HookController::class, 'cmd']);
Route::any('/api/tg/hook/cmd_ffq_article', [HookController::class, 'cmd_ffq_article']);

Route::get('/tool', [SakaiController::class, 'tool']);
