<?php namespace Elegant;
use Illuminate\Database\Eloquent\Model;
abstract class Elegant extends Model {
	public $timestamps = true;
	public $autoSetCreator = 'creator_id';
	public $softDelete;
	public  $entityName;
	public $rules = array();
	private $ruleSubs = array();
	public $messages = array();
	public $errors;
	public $modelName = null;
	public $urlbase = null;
	public $processorOpts= array();
	public $processors = [];
	public $entity = null;
	public $useCache = true;
	public $ttl = 20; // Time To Live - for cache
	protected $url = array();

	public function __construct($attributes = array())
	{
		 parent::__construct($attributes);
		 $this->errors = new Laravel\Messages();    // initialize empty messages object
		 $this->modelName = get_class($this);
		  if(!is_null($this->entityName))
		 {
		 	$entityName = $this->entityName;
			$this->entity = new $entityName($this);
		 }
		 call_user_func ( [$this, '_initialize' ]);
	}
	public function _initialize(){}
	// /* Creator ****************************/
	// public function creator()
	// {
	// 	if($foreignId = static::$autoSetCreator)
	// 		return $this->belongs_to('User', $foreignId);
	// }
	// public function set_creator($user = null)
	// {
	// 	if(is_object($user))
	// 		$user = $user->id;
	// 	$this->set_attribute('creator_id', $user);
	// }

	// public function auto_set_creator(){
	// 	$this->set_attribute(static::$autoSetCreator, Auth::user()->id);
	// }
	// public function url($type) {
	// 	if($this->exists){
	// 		$this->_init_urls();
	// 		return $this->url[$type];
	// 	}
	// 	return null;
	// }

	// public function _init_urls() {
	// 	$base = static::$urlbase;
	// 	if($base){
	// 		$id = $this->key;
	// 		$this->url['edit'] = action("{$base}@edit", [$id]);
	// 		$this->url['view'] = action("{$base}@view", [$id]);
	// 		$this->url['delete'] = action("{$base}@view", [$id]);
	// 	}
	// }
	// /* Save ****************************/
	// public function preNew() {}
	// public function postNew() {}
	// public function preSave() { return true; }
	// public function postSave()
	// {
	// 	if(static::$useCache)
	// 		Cache::forget(static::_get_cache_key($this->id));
	// }
	// public function save($validate=true, $preSave=null, $postSave=null)
	// {
	// 	$newRecord = !$this->exists;
	// 	if ($validate)
	// 		if (!$this->valid()) return false;
	// 	if($newRecord)
	// 		$this->preNew();
	// 	if (static::$autoSetCreator)
	// 		$this->auto_set_creator();
	// 	$before = is_null($preSave) ? $this->preSave() : $preSave($this);
	// 	  // check before & valid, then pass to parent
	// 	$success = ($before) ? parent::save() : false;
	// 	if ($success)
	// 		is_null($postSave) ? $this->postSave() : $postSave($this);
	// 	if($newRecord)
	// 		$this->postNew();
	// 	return $success;
	// }
	// public function onForceSave(){}
	// public function forceSave($validate=true, $rules=array(), $messages=array(), $onForceSave=null)
	// {
	// 	if ($validate)
	// 		$this->valid($rules, $messages);
	// 	 $before = is_null($onForceSave) ? $this->onForceSave() : $onForceSave($this);  // execute onForceSave
	// 	 return $before ? parent::save() : false; // save regardless of the result of validation
	// }
	// /* Delete ****************************/
	// public function softDelete($val = true, $preDelete=null, $postDelete=null)
	// {
	// 	if ($this->exists)
	// 	{
	// 		$before = is_null($preDelete) ? $this->preDelete() : $preDelete($this);
	// 		$success = null;
	// 		if($before) {
	// 			$this->set_attribute($this->softdelete, $val);
	// 			$success = $this->save(false);
	// 		}
	// 		else
	// 			$success = false;
	// 		if ($success)
	// 			is_null($postDelete) ? $this->postDelete() : $postDelete($this);
	// 		return $success;
	// 	}
	// }
	// public function preDelete()  { return true;}
	// public function postDelete(){}
	// public function delete($softDelete = true, $preDelete=null, $postDelete=null)
	// {
	// 	if ($this->softdelete and isset($this->original[$this->softdelete]))
	// 		$success = $this->softDelete($softDelete, $preDelete, $postDelete);
	// 	else
	// 		$success = $this->hardDelete($preDelete, $postDelete);
	// 	if($success and static::$useCache)
	// 		Cache::forget(static::_get_cache_key($this->id));
	// 	return $success;
	// }
	// /* Hard Delete ****************************/
	// public function preHardDelete() {  return true;  }
	// public function postHardDelete()  { }
	// public function hardDelete( $preHardDelete=null, $postHardDelete=null)
	// {
	// 	if ($this->exists)
	// 	{
	// 		$before = is_null($preHardDelete) ? $this->preHardDelete() : $preHardDelete($this);
	// 		$success = ($before) ? parent::delete() : false;
	// 		if ($success)
	// 			is_null($postHardDelete) ? $this->postHardDelete() : $postHardDelete($this);
	// 		return $success;
	// 	}
	// }

	// /* Validate ****************************/
	// public function valid( $rules=array(), $messages=array())
	// {
	// 	 $valid = true;// innocent until proven guilty
	// 	 if(!empty($rules) || !empty(static::$rules))
	// 	 {
	// 		$rules = (empty($rules)) ? static::$rules : $rules;// check for overrides
	// 		if (!empty($this->ruleSubs))
	// 			$rules = $this->ruleSubs +  $rules;
	// 		$messages = (empty($messages)) ? static::$messages : $messages;
	// 		if ($this->exists) // if the model exists, this is an update
	// 		{
	// 			$data = $this->get_dirty();
	// 			$rules = array_intersect_key($rules, $data); // so just validate the fields that are being updated
	// 		}
	// 		else
	// 		 	$data = $this->attributes;// otherwise validate everything!

	// 		$validator = Validator::make($data, $rules, $messages);// construct the validator
	// 		$valid = $validator->valid();

	// 		if($valid) // if the model is valid, unset old errors
	// 		$this->errors->messages = array();
	// 		else // otherwise set the new ones
	// 		$this->errors = $validator->errors;
	// 	}
	// 	return $valid;
	// }
 //  /**
	// * Set an attribute's value on the model.
	// *
	// * @param  string  $key
	// * @param  mixed   $value
	// * @return void
	// */
	// public function set_attribute($key, $value)
	// {
	// 	if(isset(static::$processors[$key])){
	// 		$processor = static::$processors[$key];
	// 		$value = $processor->setData($value)->runActions()->getData();
	// 	}
	// 	return parent::set_attribute($key, $value);
	// }

	// public function get_attribute($key)
	// {
	// 	$value = parent::get_attribute($key);
	// 	return $value;
	// }

	// public function only($atts = array())
	// {
	// 	$atts = (array) $atts;
	// 	$output = array_flip($atts );
	// 	foreach ($atts as $name)
	// 		$output[$name] = $this->$name;
	// 	return $output;
	// }
	// public function onlyFluent($atts = array())
	// {
	// 	return  new \Laravel\Fluent( $this->only($atts) );
	// }

	// public function __set($key, $value)
	// {
	// 	if (!array_key_exists($key, $this->attributes) || $value !== $this->$key)
	// 	{
	// 		parent::__set($key, $value);
	// 	}

	// }

	// public function __get($key)
	// {
	// 	if(!is_null($this->entity))
	// 	{
	// 		if($this->entity->hasAttribute($key))
	// 			return $this->entity->$key;
	// 		if($this->entity->$key)
	// 			return $this->entity->$key;
	// 	}

	// 	return parent::__get($key);
	// }
	// private function _get_cache_key($id)
	// {
	// 	return 'model_'.static::table().'_'.$id;
	// }
	// private function getAttrProcessorInfo($attrOpts){
	// 	$attrOpts = (array) $attrOpts;
	//  	$actions = [];
	//  	$settings = [];
	// 	foreach ($attrOpts as $key => $value) {
	//  			$actions[] = (is_numeric($key)) ? $value : $key;
	// 	 		if(is_array($value))
	// 	 			$settings[$key] = $value;
	// 	 	}
	//  	return new Processor( $actions,  $settings);
	// }

	// public function _find($id, $column = null, $columns = array('*'))
	// {
	// 	if(static::$useCache)
	// 	{
	// 		$cache_key = static::_get_cache_key($id);
	// 		if (Cache::has($cache_key))
	// 			return Cache::get($cache_key);
	// 	}
	// 	if (is_null($column))
	// 		$column = static::$key;
	// 	if (is_array($column))
	// 	{
	// 		foreach ($column as $r)
	// 		{
	// 			$result = $this->query()->where($r, '=', $id)->first($columns);
	// 			if( $result )
	// 				return $result;
	// 		}
	// 		return null;
	// 	}
	// 	else
	// 		return $this->query()->where($column, '=', $id)->first($columns);
	// }

	// /* STATIC FUNCTIONS ****************************/
	// public static function dne($id)
	// {
	// 	if (static::find($id))
	// 		return false;
	// 	return true;
	// }
	// public static function isTrashed($id)
	// {
	// 	$o = static::find($id);
	// 	return $o->delete;
	// }
	// public static function all($includeDeleted = true){
	// 	if($this->softdelete and $includeDeleted)
	// 		return static::deleted(0)->get();
	// 	return parent::all();
	// }

	// public static function deleted($val =1){
	// 	$delete = "where_".$this->softdelete;
	// 	return static::{$delete}($val);
	// }

	// public static function property_array($objs, $propId = 'id', $prop= null)
	// {
	// 	if(!is_array($orig = $objs))
	// 		$objs = array($orig);
	// 	$output = [];
	// 	foreach ($objs as $o)
	// 	{
	// 		if($prop)
	// 			$output[$o->{$propId}] = $o->{$prop};
	// 		else
	// 			array_push($output, $o->{$propId});
	// 	}

	// 	return $output;
	// }


}
