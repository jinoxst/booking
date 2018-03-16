<?php

namespace App\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

/**
 * include時の時間を取得しておく。
 */
if (!defined('APP_START_TIME')) {
    define('APP_START_TIME', microtime(true));
}

/**
 * アプリケーションログ設定クラス
 */
class ApplicationLog
{
    /**
     * 警告閾値：利用メモリ（MB）
     */
    const THRESHOLD_WARNING_MEMORY = 100;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $this->app = $app;

        // テスト時はアプリケーションログなし(.env => APP_ENV == env('testing'))
        if (!$app->environment('testing')) {
            // 起動ログ
            $this->requestStart();
        }
    }

    /**
     * 起動ログ
     */
    public function requestStart()
    {
        if ($this->app->runningInConsole()) {
            $command = sprintf('php %s', implode(' ', (array)array_get($GLOBALS, 'argv')));
            $params = compact('command');
            app('log')->info('REQ START', $params);
        } else {
            // シャットダウンハンドラ追加：シャットダウンログ
            register_shutdown_function([$this, 'requestEnd']);
            $method = request()->method();
            $uri = request()->path();
            $ip = request()->ip();
            $log_id = APP_START_TIME;
            $log_only = app('bootstrap-log-only');
            $params = compact('method', 'uri', 'ip', 'log_id');
            if(in_array($uri, $log_only)){
                app('log')->info('REQ START', $params);
            }
        }
    }

    /**
     * シャットダウンログ
     *
     * @return void
     */
    public function requestEnd()
    {
        // シャットダウンログを出力
        $time = $this->getProcessingTime();
        $memory = $this->getPeekMemory();
        if ($this->isOverUsingMemory(self::THRESHOLD_WARNING_MEMORY)) {
            app('log')->warning('using a large amount of memory.', compact('memory'));
        }
        $method = request()->method();
        $uri = request()->path();
        $ip = request()->ip();
        $log_id = APP_START_TIME;
        $params = compact('method', 'uri', 'ip', 'log_id', 'time', 'memory');
        $log_only = app('bootstrap-log-only');
        if(in_array($uri, $log_only)){
            app('log')->info('REQ END', $params);
        }
        // app('log')->info('REQ END', $params);
    }

    /**
     * 単位をつけて見やすくした時間を返す
     *
     * @param float $time マイクロ秒数
     * @return string
     */
    protected function formatTime($time)
    {
        if ($time < 1) {
            return sprintf('%0.3f[ms]', $time * 1000);
        } else if ($time < (1 / 1000)) {
            return sprintf('%0.3f[μs]', $time * 1000 * 1000);
        }
        return sprintf('%0.3f[s]', $time);
    }

    /**
     * 単位をつけて見やすくしたメモリを返す
     *
     * @param float $value
     * @return string
     */
    protected function formatMemory($value)
    {
        if ($value < 1024 * 10) {
            // 10KBまではB表示
            return sprintf('%s[b]', $value);
        } else if ($value < (1024 * 1024 * 10)) {
            // 10MBまではKB表示
            return sprintf('%s[kb]', round($value / 1024, 1));
        } else {
            return sprintf('%s[mb]', round($value / (1024 * 1024), 2));
        }
    }

    /**
     * @param int $threshold 閾値（MB）
     * @return bool
     */
    protected function isOverUsingMemory($threshold)
    {
        return round(memory_get_peak_usage(1) / 1024 / 1024) > $threshold;
    }

    /**
     * 処理時間を取得
     *
     * @return string
     */
    protected function getProcessingTime()
    {
        return $this->formatTime(microtime(true) - APP_START_TIME);
    }

    /**
     * メモリ使用量のピーク値を取得
     *
     * @return float
     */
    protected function getPeekMemory()
    {
        return $this->formatMemory(memory_get_peak_usage(true));
    }

}