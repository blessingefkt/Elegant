<?php namespace Elegant;
use ArrayAccess, Iterator, Countable, Serializable;
class SmartCollection extends Collection
{

	public static function make($collection, $class = null, $keyName = null)
	{
		return new static($collection, $class, $keyName);
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
