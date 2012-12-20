<?php namespace Elegant;
use Illuminate\Support\ServiceProvider;

define('ELEGANT_VERSION', '1.0.0');

class ElegantServiceProvider extends ServiceProvider {
	public function register()
	{// Register config file
		$this->app['config']->package('iyoworks/elegant', __DIR__.'/../config');
		$this->registerBindings();
	}

  /**
   * Register the application bindings.
   *
   * @return void
   */
  public function registerBindings()
  {
  	$this->app['elegant'] = $this->app->share(function($app) {
  		return new Elegant(array(), $app);
  	});
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
  	return array('elegant');
  }



}
