<?php namespace Elegant;
use Illuminate\Database\Eloquent\Model;
use Cache;
class Elegant extends Model {
	public $timestamps = true;
	public $autoSetCreator = null;
	public $softDelete;
	public $rules = array();
	private $ruleSubs = array();
	public $messages = array();
	public $errors;
	public $modelName = null;
	public $urlbase = null;
	public $entity;
	public $useCache = true;
	public $ttl = 20; // Time To Live - for cache
	protected $url = array();

	public function __construct($attributes = array())
	{
		 parent::__construct($attributes);
		  // initialize empty messages object
		 $this->errors = new \Illuminate\Support\MessageBag();
		 $this->modelName = get_class($this);

	}
	/* Creator ****************************/
	public function creator()
	{
		if($foreignId = $this->autoSetCreator)
			return $this->belongsTo('User', $foreignId);
	}

	private function autoSetCreator(){
		$this->setAttribute($this->autoSetCreator, Auth::user()->id);
	}
	public function url($type) {
		if($this->exists){
			$this->_init_urls();
			return $this->url[$type];
		}
		return null;
	}

	public function _init_urls() {
		$base = $this->urlbase;
		if($base){
			$id = $this->key;
			$this->url['edit'] = action("{$base}@edit", [$id]);
			$this->url['view'] = action("{$base}@view", [$id]);
			$this->url['delete'] = action("{$base}@view", [$id]);
		}
	}
	/* Save ****************************/
	public function preCreate() {}
	public function postCreate() {}
	public function preSave() { return true; }
	public function postSave()
	{
		if($this->useCache)
			Cache::forget($this->getCacheKey($this->id));
	}
	public function save($validate=true, $preSave=null, $postSave=null)
	{
		$newRecord = !$this->exists;
		if ($validate)
			if (!$this->valid()) return false;
		if($newRecord)
			$this->preCreate();
		if ($this->autoSetCreator)
			$this->autoSetCreator();
		$before = is_null($preSave) ? $this->preSave() : $preSave($this);
		  // check before & valid, then pass to parent
		$success = ($before) ? parent::save() : false;
		if ($success)
			is_null($postSave) ? $this->postSave() : $postSave($this);
		if($newRecord)
			$this->postCreate();
		return $success;
	}
	public function onForceSave(){}
	public function forceSave($validate=true, $rules=array(), $messages=array(), $onForceSave=null)
	{
		if ($validate)
			$this->valid($rules, $messages);
		 $before = is_null($onForceSave) ? $this->onForceSave() : $onForceSave($this);  // execute onForceSave
		 return $before ? parent::save() : false; // save regardless of the result of validation
	}
	/* Soft Delete ****************************/
	public function preSoftDelete() {  return true;  }
	public function postSoftDelete()  { }
	public function softDelete($val = true, $preSoftDelete=null, $postSoftDelete=null)
	{
		if ($this->exists)
		{
			$before = is_null($preSoftDelete) ? $this->preSoftDelete() : $preSoftDelete($this);
			$success = null;
			if($before) {
				$this->setAttribute($this->softDelete, $val);
				$success = $this->save(false);
			}
			else
				$success = false;
			if ($success)
			{
				is_null($postSoftDelete) ? $this->postSoftDelete() : $postSoftDelete($this);
				if($success and $this->useCache)
						Cache::forget($this->getCacheKey($this->id));
			 }
			return $success;
		}
	}

	/* Hard Delete ****************************/
	public function preDelete()  { return true;}
	public function postDelete(){}
	public function delete( $preDelete=null, $postDelete=null)
	{
		if ($this->exists)
		{
			$before = is_null($preDelete) ? $this->preDelete() : $preDelete($this);
			$success = ($before) ? parent::delete() : false;
			if ($success)
			{
			 	is_null($postDelete) ? $this->postDelete() : $postDelete($this);
			 	if($success and $this->useCache)
					Cache::forget($this->getCacheKey($this->id));
			 }
			return $success;
		}
	}

	/* Validate ****************************/
	public function valid( $rules=array(), $messages=array())
	{
		 $valid = true;// innocent until proven guilty
		 if(!empty($rules) || !empty($this->rules))
		 {
			$rules = (empty($rules)) ? $this->rules : $rules;// check for overrides
			if (!empty($this->ruleSubs))
				$rules = $this->ruleSubs +  $rules;
			$messages = (empty($messages)) ? $this->messages : $messages;
			if ($this->exists) // if the model exists, this is an update
			{
				$data = $this->get_dirty();
				$rules = array_intersect_key($rules, $data); // so just validate the fields that are being updated
			}
			else // otherwise validate everything!
			 	$data = $this->attributes;

			$validator = Validator::make($data, $rules, $messages);// construct the validator
			$valid = $validator->valid();

			if($valid) // if the model is valid, unset old errors
				$this->errors->messages = array();
			else // otherwise set the new ones
				$this->errors = $validator->errors;
		}
		return $valid;
	}

	public function __get($key)
	{
		if($this->entity())
			if($this->entity()->has($key))
				return $this->entity()->$key;
		return parent::__get($key);
	}
	private function getCacheKey($id)
	{
		return 'model_'.$this->table.'_'.$id;
	}

	public function entity(){
		if(is_string($this->entity)){
			$name = $this->entity;
			$this->entity = new $name($this);
		}
		return $this->entity;
	}
	public function isDeleted(){
		if(!is_null($this->softDelete))
			return $this->{$this->softDelete};
		else
			throw new ElegantException("Column does not exist", "The softdelete column name has not been specified for the \"{$this->modelName}\" model.");
	}
	public function deleted($val =1){
		if(!is_null($this->softDelete))
			return $this->newQuery()->where($this->softDelete, '=',$val);
		else
			throw new ElegantException("Column does not exist", "The softdelete column name has not been specified for the \"{$this->modelName}\" model.");
	}
	/**
	 * Convert the model instance to an array.
	 * @return array
	 */
	public function toArray()
	{
		$attributes = parent::toArray();
		if(!is_null($this->entity()))
			$attributes = array_merge($attributes, $this->entity()->toArray());
		return $attributes;
	}
	/**
	 * Wrap a collection of objects with an array
	 * @return array
	 */
	public function arrayWrap($collection)
	{
		$collection = (array) $collection;
		return array_pop($collection);
	}

	/* STATIC FUNCTIONS ****************************/
	public static function dne($id)
	{
		if (static::find($id))
			return false;
		return true;
	}
	public static function all($excSoftDeletes= true){
		$instance = new static;
		if(!is_null($instance->softDelete) and $excSoftDeletes)
			return static::discarded(0)->get();
		return parent::all();
	}

	public static function discarded($val =1){
		$instance  = new static;
		return $instance->deleted($val);
	}

	public static function find($value, $columns = array('*'))
	{
		$instance = new static;
		if($instance->useCache)
		{
			$cache_key = $instance->getCacheKey($value);
			if (Cache::has($cache_key))
				return Cache::get($cache_key);
		}
		if (is_array($columns))
			return $instance->newQuery()->find($value, $columns );
		if(is_string($columns))
			return $instance->newQuery()->where($columns, '=', $value)->first();
		return null;
	}

	public static function findFirst($col, $val){
		$instance = new static;
		return $instance->newQuery()->where($col, '=',$val)->first();
	}

}
