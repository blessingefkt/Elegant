<?php namespace Elegant;
class Collection implements ArrayAccess, Iterator, Countable, Serializable
{
	protected $_position = 0;
	public $class;
	protected $container = array();

	public static function make($collection, $class = null)
	{
		return new static($collection, $class);
	}

	public function __construct($collection, $class)
	{
		if($class){
			$this->class = $class;
			$this->container = array_map(function($item) use ($class){
				return new $class($item);
			}, $collection);
		}
		else
			$this->container = $collection;
	}
	public function add($item)
	{
		$this->container[] = $item;
	}
	public function first()
	{
		return count($this->container) > 0 ? reset($this->container) : null;
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
