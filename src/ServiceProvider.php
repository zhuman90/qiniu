<?php

/*
 * This file is part of the laravuel/qiniu.
 *
 * (c) laravuel <45761113@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Laravuel\Qiniu;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $source = __DIR__.'/config.php';
        $this->publishes([
            $source => config_path('qiniu.php')
        ]);
        $this->mergeConfigFrom($source, 'qiniu');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('qiniu', function($app) {
            return new Qiniu(config('qiniu'));
        });
    }

    /**
     * 取得提供者提供的服务
     *
     * @return array
     */
    public function provides()
    {
        return [
            'qiniu'
        ];
    }
}
