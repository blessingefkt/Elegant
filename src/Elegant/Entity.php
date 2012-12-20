<?php namespace Elegant;
abstract class Entity {
	public  $model = null;
	public static $modelName = 'resource';

	public function __construct($model)
	{
		 $this->model and $this->{$this->modelName} = $model;
	}

	public function has($key){
		return method_exists($this, $key);
	}
	public function __get($key)
	{
		if($this->has($key))
			return $this->$key();
	}
}
