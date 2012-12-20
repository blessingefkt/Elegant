<?php namespace Elegant\Facades;

use Illuminate\Support\Facades\Facade;

class Elegant extends Facade {

	/**
	 * Get the registered component.
	 *
	 * @return object
	 */
	protected static function getFacadeAccessor(){ return 'elegant'; }

}
