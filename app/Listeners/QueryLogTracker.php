<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\QueryExecuted;
use DateTime;
use DB;

class QueryLogTracker
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param QueryExecuted $event
     * @internal param $query
     * @internal param $bindings
     * @internal param $time
     */
    public function handle(QueryExecuted $event)
    {
        // クエリ情報を見やすく整形
        $sql = $event->sql;
        $time = $event->time;
        $bindings = $event->bindings;
        foreach ($bindings as $i => $binding) {
            if ($binding instanceof DateTime) {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else if (is_string($binding)) {
                $bindings[$i] = DB::getPdo()->quote($binding);
            } else if (null === $binding) {
                $bindings[$i] = 'null';
            }
        }
        $query = str_replace(array('%', '?', "\r", "\n", "\t"), array('%%', '%s', ' ', ' ', ' '), $event->sql);
        $query = preg_replace('/\s+/uD', ' ', $query);
        $query = vsprintf($query, $bindings) . ';';

        // ログへ出力
        app('sql-log')->debug($query, compact('time'));
    }
}
