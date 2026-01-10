<?php

declare(strict_types=1);

namespace App\Services\TomTool;

use Throwable as ThrowableBase;
use Illuminate\Database\QueryException;
use App\Services\TomTool\TelegramVdb;

/**
 
 new:
    $oThrowableHandler = ThrowableHandler::make($e);
 
 快速：
    $aResult = ThrowableHandler::make($e)->toArr();
 
 action：
    fetch() or get()
 
 set:
    enableTg()
    enableTrace()
    setMaxDepth()
 
 */

class ThrowableHandler
{
    protected bool $tgEnable = false;
    protected bool $showTrace = false;
    protected int $maxDepth = 3;
    protected ThrowableBase $e;

    public static function make(ThrowableBase $e): self
    {
        $oInstance = new self();
        $oInstance->e = $e;
        return $oInstance;
    }
    
    public function fetch()
    {
        return $this->get();
    }
    
    public function get(): array
    {
        $data = $this->buildExceptionData($this->e, 0);

        if ($this->tgEnable) {
            $this->sendTelegram($data);
        }

        return $data;
    }
    
    public function toArr(): array
    {
        return $this->get();
    }

    public function toJson(): string
    {
        return json_encode($this->get(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function enableTg(bool $enable = true): self
    {
        $this->tgEnable = $enable;
        return $this;
    }

    public function enableTrace(bool $enable = true): self
    {
        $this->showTrace = $enable;
        return $this;
    }

    public function setMaxDepth(int $depth): self
    {
        $this->maxDepth = max(1, $depth);
        return $this;
    }

    protected function buildExceptionData(ThrowableBase $e, int $depth): array
    {
        $data = [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ];

        $code = $e->getCode();
        if ($code !== null && $code !== 0) {
            $data['code'] = $code;
        }

        if ($this->showTrace) {
            $filteredTrace = array_filter($e->getTrace(), function ($frame) {
                return empty($frame['file']) || !str_contains($frame['file'], '/vendor/');
            });

            $traceLines = [];
            foreach ($filteredTrace as $frame) {
                $file = $frame['file'] ?? '[internal]';
                $line = $frame['line'] ?? '';
                $func = isset($frame['class'], $frame['function'])
                    ? "{$frame['class']}{$frame['type']}{$frame['function']}"
                    : ($frame['function'] ?? '[unknown]');
                $traceLines[] = "{$file}:{$line} {$func}()";
            }

            $data['trace'] = implode("\n", $traceLines);
        }

        if ($depth < $this->maxDepth && $e->getPrevious() instanceof ThrowableBase) {
            $data['previous'] = $this->buildExceptionData($e->getPrevious(), $depth + 1);
        }

        return $data;
    }

    protected function sendTelegram(array $data): void
    {
        TelegramVdb::make()->aData($data)->save();
    }
}

