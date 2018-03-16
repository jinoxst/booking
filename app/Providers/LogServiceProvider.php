<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Illuminate\Log\Writer;
// 差し替え
use App\Utils\Monolog\Writer;
// 追加
// use App\Utils\Monolog\Processor\IntrospectionProcessor;
// use App\Utils\Monolog\Processor\ProcessIdProcessor;
use Monolog\Logger as Monolog;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    // public function boot()
    // {
    //     //
    // }

    /**
     * Register the application services.
     *
     * @return void
     */
    // public function register()
    // {
    //     //
    // }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('log', function () {
            return $this->createAppLogger();
        });
        $this->app->singleton('sql-log', function () {
            return $this->createSqlLogger();
        });
        $this->app->singleton('bootstrap-log-only', function () {
            return $this->findBootstrapLogOnly();
        });
    }

    public function findBootstrapLogOnly(){
        $arr = [];
        $classes = [];
        $routeCollection = app('router')->getRoutes();
        foreach ($routeCollection as $route) {
            $action = $route->getActionName();
            $appAction = $action;
            $action = substr($action, strrpos($action, '\\') + 1);
            $arr[$action] = $route->uri();

            $appAction = substr($appAction, 0, strrpos($appAction, '@'));
            if(!in_array($appAction, $classes)){
                array_push($classes, $appAction);
            }
        }

        $log_only_arr = [];
        foreach($classes as $cls){
            $clsOnly = substr($cls, strrpos($cls, '\\') + 1);
            foreach(app($cls)->bootstrap_log_only as $bts){
                if(isset($arr[$clsOnly.'@'.$bts])){
                    if(!in_array($arr[$clsOnly.'@'.$bts], $log_only_arr)){
                        array_push($log_only_arr, $arr[$clsOnly.'@'.$bts]);
                    }
                }
            }
        }
        return $log_only_arr;
    }

    // public function findBootstrapLogOnly(){
    //     $classes = [];
    //     $this->findFiles(app_path('Http/Controllers'), $classes);
    //     $only_arr = [];
    //     foreach($classes as $cls){
    //         $cls = str_replace(base_path(), '', $cls);
    //         $cls = str_replace('.php', '', $cls);
    //         $cls = substr($cls, 1, strlen($cls) - 1);
    //         $cls = ucfirst($cls);
    //         $cls = str_replace('/', '\\', $cls);

    //         if(isset(app($cls)->bootstrap_log_only)){
    //             foreach(app($cls)->bootstrap_log_only as $bts){
    //                 if(!in_array($bts, $only_arr)){
    //                     array_push($only_arr, $bts);
    //                 }
    //             }
    //         }
    //     }
    //     return $only_arr;
    // }

    private function findFiles($d, &$classes){
        foreach(scandir($d) as $f){
            if($f != '.' && $f != '..'){
                if(is_dir($d.'/'.$f)){
                    $this->findFiles($d.'/'.$f, $classes);
                }else{
                    array_push($classes, $d.'/'.$f);
                }
            }
        }
    }

    /**
     * Create the app logger.
     *
     * @return \Illuminate\Log\Writer
     */
    public function createAppLogger()
    {
        $log = new Writer(
            new Monolog($this->channel()), $this->app['events']
        );
        // プロセッサーを登録
        // $processors = [
        //     new ProcessIdProcessor(),
        //     new IntrospectionProcessor()
        // ];
        // $log = new Writer(
        //     new Monolog($this->channel(), [], $processors), $this->app['events']
        // );
        $this->configureHandler($log, 'app');
        return $log;
    }

    /**
     * Create the sql logger.
     *
     * @return \Illuminate\Log\Writer
     */
    public function createSqlLogger()
    {
        $log = new Writer(
            new Monolog($this->channel()), $this->app['events']
        );
        // プロセッサーを登録
        // $processors = [
        //     new ProcessIdProcessor(),
        // ];
        // $log = new Writer(
        //     new Monolog($this->channel(), [], $processors), $this->app['events']
        // );
        $this->configureHandler($log, 'sql');
        return $log;
    }

    /**
     * Get the name of the log "channel".
     *
     * @return string
     */
    protected function channel()
    {
        return $this->app->bound('env') ? $this->app->environment() : 'production';
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @param string $base_name
     * @return void
     */
    protected function configureHandler(Writer $log, $base_name)
    {
        $this->{'configure'.ucfirst($this->handler()).'Handler'}($log, $base_name);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @param string $base_name
     * @return void
     */
    protected function configureSingleHandler(Writer $log, $base_name)
    {
        $log->useFiles(
            sprintf('%s/logs/%s%s.log', $this->app->storagePath(), $this->getFilePrefix(), $base_name),
            $this->logLevel()
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @param string $base_name
     * @return void
     */
    protected function configureDailyHandler(Writer $log, $base_name)
    {
        $log->useDailyFiles(
            sprintf('%s/logs/%s%s.log', $this->app->storagePath(), $this->getFilePrefix(), $base_name),
            $this->maxFiles(),
            $this->logLevel()
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @param string $base_name
     * @return void
     */
    protected function configureSyslogHandler(Writer $log, $base_name)
    {
        $log->useSyslog($base_name, $this->logLevel());
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @param string $base_name
     * @return void
     */
    protected function configureErrorlogHandler(Writer $log, $base_name)
    {
        $log->useErrorLog($this->logLevel());
    }

    /**
     * Get the default log handler.
     *
     * @return string
     */
    protected function handler()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log', 'single');
        }

        return 'single';
    }

    /**
     * Get the log level for the application.
     *
     * @return string
     */
    protected function logLevel()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_level', 'debug');
        }

        return 'debug';
    }

    /**
     * Get the maximum number of log files for the application.
     *
     * @return int
     */
    protected function maxFiles()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_max_files', 5);
        }

        return 0;
    }

    /**
     * ファイルプレフィックスを取得
     *
     * @return string
     */
    private function getFilePrefix()
    {
        return php_sapi_name() == 'cli' ? 'cli_' : '';
    }
}
