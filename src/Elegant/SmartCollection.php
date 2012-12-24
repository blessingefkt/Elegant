<?php namespace Elegant;
use ArrayAccess, Iterator, Countable, Serializable;
class SmartCollection extends Collection
{
	protected $_position = 0;
	public $class;
	protected $container = array();

	public static function make($collection, $class = null)
	{
		return new static($collection, $class);
	}

	public function __get($key){
		return $this->offsetGet($key);
	}

	public function __set($key, $value){
		if(!is_null($this->class))
			$value = $this->newInstance($value);
		return $this->offsetSet($key, $value);
	}

}
