<?php namespace Elegant;
abstract class Entity {
	public  $model = null;
	public static $modelName = 'resource';
	private $attributes;

	public function __construct($model)
	{
		$this->model  = $model;
		$this->{static::$modelName} = $this->model;
	}

	public function has($key){
		return method_exists($this, $key);
	}
	public function __get($key)
	{
		if($this->has($key))
			return $this->$key();
	}
	public function toArray() {
		$ents =  array_map( function($item)  {
			return $item;
		},  get_class_methods($this) );
		$output = array();
		foreach ($ents as $k)
			$output[$k] = $this->$k();
		return $output;
	}
	public function attributes()
	{
		if(is_null($this->attributes))
			$this->attributes = $this->toArray();
		return $this->attributes;
	}
	public function toJson(){
		return json_encode($this->toArray());
	}
}
