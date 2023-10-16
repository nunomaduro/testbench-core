<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Contracts\Config;

/**
 * @internal
 */
class StartWorkbench
{
    /**
     * The project configuration.
     *
     * @var \Orchestra\Testbench\Contracts\Config
     */
    public $config;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->singleton(Config::class, function () {
            return $this->config;
        });
    }
}
