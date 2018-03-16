<?php

namespace App\Classes;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class CommonLogger {
    private $handler;

    function __construct() {
        $handler = new RotatingFileHandler(storage_path('/logs/'.php_sapi_name().'.log'), config('app.log_max_files'), Logger::DEBUG);
        $output = "[%datetime%] [%level_name%] %channel% - %message%\n";
        $formatter = new LineFormatter($output, 'Y-m-d H:i:s');
        $handler->setFormatter($formatter);
        $this->handler = $handler;
    }

    function getHandler(){
        return $this->handler;
    }
}