<?php

namespace App;

use App\Providers\LogServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;

/**
 * アプリケーションクラス
 * ログのサービスプロバイダを変更したいため、overwrite
 *
 * @package app.Providers
 */
class Application extends \Illuminate\Foundation\Application
{

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));

        // ここを差し替え
        $this->register(new LogServiceProvider($this));

        $this->register(new RoutingServiceProvider($this));
    }
}