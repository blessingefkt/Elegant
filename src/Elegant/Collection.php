<?php namespace Elegant;
use ArrayAccess, Iterator, Countable, Serializable;
class Collection implements ArrayAccess, Iterator, Countable, Serializable
{
	protected $_position = 0;
	public $class;
	public $keyName;
	protected $container = array();

	public static function make($collection, $class = null, $keyName = null)
	{
		return new static($collection, $class, $keyName);
	}

	public function __construct($collection, $class =null, $keyName = null)
	{
		if($keyName)
			$this->keyName = $keyName;
		if($class)
			$this->class = $class;
		$this->addObject($collection, $keyName);
	}
	public function addObject($collection, $keyName = null)
	{
		if(!is_null($keyName))
			$keyName = $this->keyName;
		if($collection instanceOf \Illuminate\Database\Eloquent\Collection)
			$collection = $collection->all();
		$collection = array_walk($collection, function($item, $k) use($keyName){
			if($keyName)
				$k = $item->$keyName;
			$this->add($item, $k);
		});
	}
	public function add($item, $index = null)
	{
		if(!is_null($this->class))
		{
			$class = $this->class;
			$item = new $class($item);
		}
		$this->offsetSet($index, $item);
	}
	public function first()
	{
		return count($this->container) > 0 ? reset($this->container) : null;
	}
	public function arrayWrap()
	{
		return (array)$this->container;
	}
	public function toArray()
	{
		return array_map(function($value)
		{
			return (array) $value;

		}, $this->container);
	}

	public function toJson()
	{
		return json_encode($this->toArray());
	}
	public function all()
	{
		return $this->container;
	}
	public function rewind()
	{
		$this->_position = 0;
	}

	public function current()
	{
		return $this->container[$this->_position];
	}

	public function key()
	{
		return $this->_position;
	}

	public function next()
	{
		++$this->_position;
	}

	public function valid()
	{
		return isset($this->container[$this->_position]);
	}

	public function count()
	{
		return count($this->container);
	}

	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
		{
			$this->container[] = $value;
		}
		else
		{
			$this->container[$offset] = $value;
		}
	}

	public function offsetExists($offset)
	{
		return isset($this->container[$offset]);
	}

	public function offsetUnset($offset)
	{
		unset($this->container[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	public function serialize()
	{
		return serialize($this->container);
	}

	public function unserialize($collection)
	{
		$this->container = unserialize($collection);
	}
}
