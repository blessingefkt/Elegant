<?php namespace IyoWorks\Elegant;

use Illuminate\Support\ServiceProvider;

define('ELEGANT_VERSION', '1.0.0');

class ElegantServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the package configuration with the loader.
		// $this->app['config']->package('jasonlewis/elegant', __DIR__.'/../config');

		// Because Laravel doesn't actually set a public path here we'll define out own. This may become
		// a limitation and hopefully will change at a later date.
		$this->app['path.public'] = realpath($this->app['path.base'].'/vendors/iyoworks/elegant');
	}

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	// public function boot()
	// {
	// 	$this->registerRoutes();
	// }



}
