<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Cron\HttpQueue as CronHttpQueue;
use App\Services\Cron\Book as CronBook;
use App\Services\Cron\FreenodeCrawl as CronFreenodeCrawl;
use App\Services\Cron\FreenodePool as CronFreenodePool;
use App\Services\Cron\FreenodeFile as CronFreenodeFile;
use App\Services\Cron\FreenodeMerge as CronFreenodeMerge;
use App\Services\Cron\FreenodeLog as CronFreenodeLog;
use App\Services\Cron\FreenodeSync as CronFreenodeSync;
use App\Services\Cron\ArticleSchema as CronArticleSchema;
use App\Services\Cron\FnTgPublish as CronFnTgPublish;
use App\Services\TomTool\Telegram\Slave as TeleSlave;

final class Cron
{
    public bool $bIsTest = false;
    public ?int $iTestH = null;
    public ?int $iTestI = null;
    public ?int $iTestS = null;

    public function run(): void
    {
        try {
            
            $h = (int) date("H");
            $i = (int) date("i");
            $s = (int) date("s");

            if ($this->bIsTest) {
                $h = (int) ($this->iTestH ?? $h);
                $i = (int) ($this->iTestI ?? $i);
                $s = (int) ($this->iTestS ?? $s);
            }
            
            echo "cron开始执行 \n";

            if ($h % 8 === 0 && $i === 3) {
                CronBook::randbox_notify();
            }
            
            if ($h % 6 === 0 && $i === 3) {
                CronFreenodeCrawl::run();
                CronFreenodePool::save();
                CronFreenodeMerge::save();
                CronFreenodeMerge::baseToSite();
                CronFreenodeSync::run();
            }

//            if ($i === 0) {
//                // ReportSender::handle();
//            }
            
            if ($h == 5 && $i === 3) {
                CronFreenodeFile::clear();
            }
            
//            if ($h == 15 && $i == 3) {
////                CronFreenodeLog::fromLast();
//            }
            
            if ($h == 11 && $i == 3) { // 中午12点03发布fn的tg
                CronFnTgPublish::run();
            }
            
            if ($i % 3 === 0) {
                CronArticleSchema::cacheTgSave();
                CronArticleSchema::pushToSlave();
                CronHttpQueue::pop();
            }
            
        } catch (\Throwable $e) {
            
            $sMsg = "";
            $sMsg .= "--- cron 异常 ---\n";
            $sMsg .= "类型: " . get_class($e) . "\n";
            $sMsg .= "信息: " . $e->getMessage() . "\n";
            $sMsg .= "文件: " . $e->getFile() . " 在第 " . $e->getLine() . " 行\n";
            $sMsg .= "代码跟踪:\n" . $e->getTraceAsString() . "\n";
            $sMsg .= "-----------------------------\n";
            
            echo $sMsg;
            
            $sMsg = "";
            $sMsg .= "--- cron 异常 ---\n";
            $sMsg .= "类型: " . get_class($e) . "\n";
            $sMsg .= "信息: " . $e->getMessage() . "\n";
            $sMsg .= "文件: " . $e->getFile() . " 在第 " . $e->getLine() . " 行\n";
            
            TeleSlave::fail()->send($sMsg);

        }
        
    }

    public function setTestH(int $h): void
    {
        $this->enableTestMode();
        $this->iTestH = $h;
    }

    public function setTestI(int $i): void
    {
        $this->enableTestMode();
        $this->iTestI = $i;
    }

    public function setTestS(int $s): void
    {
        $this->enableTestMode();
        $this->iTestS = $s;
    }

    public function enableTestMode(bool $enable = true): void
    {
        $this->bIsTest = $enable;
    }
}
