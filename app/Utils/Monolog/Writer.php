<?php

namespace App\Utils\Monolog;

use Illuminate\Log\Writer as BaseWriter;
use Monolog\Formatter\LineFormatter;

/**
 * Monolog用Writer
 * 日時をマイクロ秒まで表示
 *
 * @package app.Utils.Monolog
 */
class Writer extends BaseWriter
{

    /**
     * Get a default Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, 'Y-m-d H:i:s.u', true, true);
    }
}