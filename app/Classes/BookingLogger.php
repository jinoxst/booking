<?php

namespace App\Classes;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class BookingLogger {
    protected $handler;

    function __construct() {
        $handler = new RotatingFileHandler(storage_path('/logs/my_app.log'), config('app.log_max_files'), Logger::DEBUG);
        $output = "[%datetime%] [%level_name%] %channel% - %message%\n";
        $formatter = new LineFormatter($output, 'Y-m-d H:i:s');
        $handler->setFormatter($formatter);
        $this->handler = $handler;
    }

    public function debug($msg){
        $arr = debug_backtrace();
        // $logger->info(var_export($arr[0], true));
        // $logger->info(var_export($arr[1], true));
        $logger = new Logger(class_basename($arr[1]['class'].'->'.$arr[1]['function']));
        $logger->pushHandler($this->handler);
        $logger->debug($msg);
    }

    public function info($msg){
        $arr = debug_backtrace();
        $logger = new Logger(class_basename($arr[1]['class'].'->'.$arr[1]['function']));
        $logger->pushHandler($this->handler);
        $logger->info($msg);
        // $logger->info(var_export($arr[0], true));
        // $logger->info(var_export($arr[1], true));
    }
}