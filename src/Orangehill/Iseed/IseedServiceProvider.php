<?php namespace Orangehill\Iseed;

use Illuminate\Support\ServiceProvider;

class IseedServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
     * Bootstrap the application events.
     *
     * @return void
     */
	public function boot()
    {
        $this->package('orangehill/iseed');
    }



+    
+    /**
+     * Register the package's component namespaces.
+     *
+     * @param  string  $package
+     * @param  string  $namespace
+     * @param  string  $path
+     * @return void
+     */
+    public function package($package, $namespace = null, $path = null)
+    {
+
+        // Is it possible to register the config?
+        if (method_exists($this->app['config'], 'package')) {
+            $this->app['config']->package($package, $path.'/config', $namespace);
+        } else {
+            // Load the config for now..
+            $config = $this->app['files']->getRequire($path .'/config/config.php');
+            foreach($config as $key => $value){
+                $this->app['config']->set($namespace.'::'.$key, $value);
+            }
+        }
+
+        // Register view files
+        $appView = $this->app['path']."/views/packages/{$package}";
+        if ($this->app['files']->isDirectory($appView))
+        {
+            $this->app['view']->addNamespace($namespace, $appView);
+        }
+
+        $this->app['view']->addNamespace($namespace, $path.'/views');
+    }


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['iseed'] = $this->app->share(function($app)
		{
			return new Iseed;
		});
		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Iseed', 'Orangehill\Iseed\Facades\Iseed');
		});

		$this->app['command.iseed'] = $this->app->share(function($app)
		{
			return new IseedCommand;
		});
		$this->commands('command.iseed');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('iseed');
	}

}
