<?php

namespace App\Utils\Monolog\Processor;

/**
 * Monolog用プロセッサ
 * 標準ProcessIdProcessorの出力キーの文字数が長いため、簡略版
 *
 * @package app.Utils.Monolog.Processor
 */
class ProcessIdProcessor
{
    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['extra']['pid'] = getmypid();

        return $record;
    }
}