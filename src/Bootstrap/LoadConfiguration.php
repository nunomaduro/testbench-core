<?php

namespace Orchestra\Testbench\Bootstrap;

use Generator;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Foundation\Workbench;
use Symfony\Component\Finder\Finder;

use function Orchestra\Testbench\workbench_path;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->instance('config', $config = new Repository([]));

        $this->loadConfigurationFiles($app, $config);

        if (\is_null($config->get('database.connections.testing'))) {
            $config->set('database.connections.testing', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'foreign_key_constraints' => Env::get('DB_FOREIGN_KEYS', false),
            ]);
        }

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return void
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $config): void
    {
        $workbenchConfig = (Workbench::configuration()->getWorkbenchDiscoversAttributes()['config'] ?? false) && is_dir(workbench_path('config'));

        foreach ($this->getConfigurationFiles($app) as $key => $path) {
            if ($workbenchConfig === true && is_file(workbench_path("config/{$key}.php"))) {
                $config->set($key, require workbench_path("config/{$key}.php"));
            } else {
                $config->set($key, require $path);
            }
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Generator
     */
    protected function getConfigurationFiles(Application $app): Generator
    {
        $path = is_dir($app->basePath('config'))
            ? $app->basePath('config')
            : realpath(__DIR__.'/../../laravel/config');

        if (\is_string($path)) {
            foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                yield basename($file->getRealPath(), '.php') => $file->getRealPath();
            }
        }
    }
}
