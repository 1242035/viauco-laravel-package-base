<?php 
namespace Viauco\Base;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Viauco\Base\Exceptions\PackageException;
use Illuminate\Support\Str;
use ReflectionClass;
/**
 * Class     ServiceProvider
 *
 * @package  Viauco\Base
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'viauco';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'base';

    /**
     * Package base path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Merge multiple config files into one instance (package name as root key)
     *
     * @var bool
     */
    protected $multiConfigs = false;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The aliases collection.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Alias loader.
     *
     * @var \Illuminate\Foundation\AliasLoader
     */
    private $aliasLoader;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->aliasLoader = AliasLoader::getInstance();

        $this->basePath = $this->resolveBasePath();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        //
    }

    /**
     * Register a binding with the container.
     *
     * @param  string|array          $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool                  $shared
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        $this->app->bind($abstract, $concrete, $shared);
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string|array          $abstract
     * @param  \Closure|string|null  $concrete
     */
    protected function singleton($abstract, $concrete = null)
    {
        $this->app->singleton($abstract, $concrete);
    }

    /**
     * Register a service provider.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @param  array                                       $options
     * @param  bool                                        $force
     *
     * @return \Illuminate\Support\ServiceProvider
     */
    protected function registerProvider($provider, array $options = [], $force = false)
    {
        return $this->app->register($provider, $options, $force);
    }

    /**
     * Register multiple service providers.
     *
     * @param  array  $providers
     */
    protected function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    /**
     * Register a console service provider.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @param  array                                       $options
     * @param  bool                                        $force
     *
     * @return \Illuminate\Support\ServiceProvider|null
     */
    protected function registerConsoleServiceProvider($provider, array $options = [], $force = false)
    {
        if ($this->app->runningInConsole())
            return $this->registerProvider($provider, $options, $force);

        return null;
    }

    /**
     * Register aliases (Facades).
     */
    protected function registerAliases()
    {
        $loader = $this->aliasLoader;

        $this->app->booting(function() use ($loader) {
            foreach ($this->aliases as $class => $alias) {
                $loader->alias($class, $alias);
            }
        });
    }

    /**
     * Add an aliases to the loader.
     *
     * @param  array  $aliases
     *
     * @return self
     */
    protected function aliases(array $aliases)
    {
        foreach ($aliases as $class => $alias) {
            $this->alias($class, $alias);
        }

        return $this;
    }

    /**
     * Add an alias to the loader.
     *
     * @param  string  $class
     * @param  string  $alias
     *
     * @return self
     */
    protected function alias($class, $alias)
    {
        $this->aliases[$class] = $alias;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Services
     | -----------------------------------------------------------------
     */

    /**
     * Get the config repository instance.
     *
     * @return \Illuminate\Config\Repository
     */
    protected function config()
    {
        return $this->app['config'];
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    protected function filesystem()
    {
        return $this->app['files'];
    }

    /**
     * Resolve the base path of the package.
     *
     * @return string
     */
    protected function resolveBasePath()
    {
        return dirname(
            (new ReflectionClass($this))->getFileName(), 2
        );
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Get config folder.
     *
     * @return string
     */
    protected function getConfigFolder()
    {
        return realpath($this->getBasePath().DIRECTORY_SEPARATOR.'config');
    }

    /**
     * Get config key.
     *
     * @return string
     */
    protected function getConfigKey()
    {
        return Str::slug($this->package);
    }

    /**
     * Get config file path.
     *
     * @return string
     */
    protected function getConfigFile()
    {
        return $this->getConfigFolder().DIRECTORY_SEPARATOR."{$this->package}.php";
    }

    /**
     * Get config file destination path.
     *
     * @return string
     */
    protected function getConfigFileDestination()
    {
        return config_path("{$this->package}.php");
    }

    /**
     * Get the base database path.
     *
     * @return string
     */
    protected function getDatabasePath()
    {
        return $this->getBasePath().DIRECTORY_SEPARATOR.'database';
    }

    /**
     * Get the migrations path.
     *
     * @return string
     */
    protected function getMigrationsPath()
    {
        return $this->getBasePath().DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
    }

    /**
     * Get the base resources path.
     *
     * @return string
     */
    protected function getResourcesPath()
    {
        return $this->getBasePath().DIRECTORY_SEPARATOR.'resources';
    }

    /**
     * Get the base views path.
     *
     * @return string
     */
    protected function getViewsPath()
    {
        return $this->getResourcesPath().DIRECTORY_SEPARATOR.'views';
    }

    /**
     * Get the destination views path.
     *
     * @return string
     */
    protected function getViewsDestinationPath()
    {
        return resource_path('views'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.$this->package);
    }

    /**
     * Get the translations path.
     *
     * @return string
     */
    protected function getTranslationsPath()
    {
        return $this->getResourcesPath().DIRECTORY_SEPARATOR.'lang';
    }

    /**
     * Get the destination views path.
     *
     * @return string
     */
    protected function getTranslationsDestinationPath()
    {
        return resource_path('lang'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.$this->package);
    }

    /* -----------------------------------------------------------------
     |  Main MethoDIRECTORY_SEPARATOR
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->checkPackageName();
    }

    /* -----------------------------------------------------------------
     |  Package MethoDIRECTORY_SEPARATOR
     | -----------------------------------------------------------------
     */

    /**
     * Register configs.
     *
     * @param  string  $separator
     */
    protected function registerConfig($separator = '.')
    {
        $this->multiConfigs
            ? $this->registerMultipleConfigs($separator)
            : $this->mergeConfigFrom($this->getConfigFile(), $this->getConfigKey());
    }

    /**
     * Register all package configs.
     *
     * @param  string  $separator
     */
    private function registerMultipleConfigs($separator = '.')
    {
        foreach (glob($this->getConfigFolder().'/*.php') as $configPath) {
            $this->mergeConfigFrom(
                $configPath, $this->getConfigKey().$separator.basename($configPath, '.php')
            );
        }
    }

    /**
     * Register commanDIRECTORY_SEPARATOR service provider.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     */
    protected function registerCommanDIRECTORY_SEPARATOR($provider)
    {
        if ($this->app->runningInConsole())
            $this->app->register($provider);
    }

    /**
     * Publish the config file.
     */
    protected function publishConfig()
    {
        $this->publishes([
            $this->getConfigFile() => $this->getConfigFileDestination()
        ], 'config');
    }

    /**
     * Publish the migration files.
     */
    protected function publishMigrations()
    {
        $this->publishes([
            $this->getMigrationsPath() => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Publish and load the views if $load argument is true.
     *
     * @param  bool  $load
     */
    protected function publishViews($load = true)
    {
        $this->publishes([
            $this->getViewsPath() => $this->getViewsDestinationPath()
        ], 'views');

        if ($load) $this->loadViews();
    }

    /**
     * Publish and load the translations if $load argument is true.
     *
     * @param  bool  $load
     */
    protected function publishTranslations($load = true)
    {
        $this->publishes([
            $this->getTranslationsPath() => $this->getTranslationsDestinationPath()
        ], 'lang');

        if ($load) $this->loadTranslations();
    }

    /**
     * Publish the factories.
     */
    protected function publishFactories()
    {
        $this->publishes([
            $this->getDatabasePath().DIRECTORY_SEPARATOR.'factories' => database_path('factories'),
        ], 'factories');
    }

    /**
     * Publish all the package files.
     *
     * @param  bool  $load
     */
    protected function publishAll($load = true)
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishViews($load);
        $this->publishTranslations($load);
        $this->publishFactories();
    }

    /**
     * Load the views files.
     */
    protected function loadViews()
    {
        $this->loadViewsFrom($this->getViewsPath(), $this->package);
    }

    /**
     * Load the translations files.
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom($this->getTranslationsPath(), $this->package);
    }

    /**
     * Load the migrations files.
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom($this->getMigrationsPath());
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */

    /**
     * Check package name.
     *
     * @throws \Arcanedev\Support\Exceptions\PackageException
     */
    private function checkPackageName()
    {
        if (empty($this->vendor) || empty($this->package)) {
            throw new PackageException('You must specify the vendor/package name.');
        }
    }
}
