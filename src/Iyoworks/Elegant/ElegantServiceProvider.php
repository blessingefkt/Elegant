<?php namespace Elegant;

use Illuminate\Support\ServiceProvider;

class ElegantServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the {{full_package}} service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('iyoworks/elegant');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
