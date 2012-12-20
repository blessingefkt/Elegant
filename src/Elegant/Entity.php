<?php namespace Elegant;
abstract class Entity {
	public  $model = null;
	public static $modelName = 'resource';

	public function __construct($model)
	{
		 $this->model and $this->{$this->modelName} = $model;
	}


}
