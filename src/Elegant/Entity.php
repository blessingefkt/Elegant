<?php namespace Elegant;
abstract class Entity {
	public  $model = null;
	public static $modelName = 'resource';
	public $excluded =  array('toArray', 'genAttributes', 'toJson', '__get', 'has', '__construct');

	public function __construct($model)
	{
		$this->model  = $model;
		$this->{static::$modelName} = $this->model;
	}

	public function has($key){
		return (method_exists($this, $key) );
	}
	public function __get($key)
	{
		if($key == 'attributes')
			return $this->genAttributes();
		if($this->has($key))
			return $this->$key();
		return parent::__get($key);
	}
	public function toArray() {
		return $this->genAttributes();
	}
	public function genAttributes()
	{
		$output = array();
		$methods = array_diff(get_class_methods($this), $this->excluded);
		foreach ($methods as $k)
			$output[$k] = $this->$k();
		return $output;
	}
	public function toJson(){
		return json_encode($this->genAttributes());
	}
}
