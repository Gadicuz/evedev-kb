<?php
/*
 * $Date$
 * $Revision$
 * $HeadURL$
 *
 * @package EDK
 */


/**
 * Cache objects between page loads.
 */
abstract class Cacheable {
	/** @var CacheHandlerHashed */
	private static $cachehandler = null;
	/** @var array */
	private static $cache = array();

	/**
	 * Create or fetch a new Cacheable object.
	 *
	 * @param string $classname A valid Cacheable class.
	 * @param integer $id The id to create/retrieve.
	 * @return mixed
	 */
	public static function factory($classname, $id)
	{
		if(!self::$cachehandler) self::init();

		if (class_exists('Config', false) && !config::get('cfg_objcache')) {
			return new $classname($id);
		} else if (isset(self::$cache[$classname.$id])) {
			return self::$cache[$classname.$id];
		} else if (self::$cachehandler->exists($classname.$id)) {
			return self::$cachehandler->get($classname.$id);
		} else {
			return new $classname($id);
		}

	}
	/**
	 * Return whether this object is cached.
	 *
	 * Uses $this->getID() as a key.
	 * @return boolean true if this object is cached.
	 */
	protected function isCached()
	{
		if (!config::get('cfg_objcache')) return false;

		if(isset(self::$cache[get_class($this).$this->getID()]))
			return true;

		if(!self::$cachehandler) self::init();
		return self::$cachehandler->exists(get_class($this).$this->getID());
	}

	/**
	 * Return a cached Cacheable.
	 *
	 * Uses $this->getID() as a key.
	 * @return Cacheable
	 */
	protected function getCache()
	{
		if (!config::get('cfg_objcache')) return false;

		if(isset(self::$cache[get_class($this).$this->getID()]))
			return self::$cache[get_class($this).$this->getID()];

		if(!self::$cachehandler) self::init();
		return self::$cachehandler->get(get_class($this).$this->getID());
	}

	/**
	 * Cache a Cacheable.
	 *
	 * Uses $this->getID() as a key.
	 * @return boolean Returns true if this was successfully cached.
	 */
	protected function putCache()
	{
		if (!config::get('cfg_objcache')) return false;

		// The unserialize/serialize is used to make a deep copy
		self::$cache[get_class($this).$this->getID()] = unserialize(serialize($this));

		if(!self::$cachehandler) self::init();
		return self::$cachehandler->put(
				get_class($this).$this->getID(), $this);
	}

	/**
	 * Delete the cached version of an object.
	 */
	 protected function delCache()
	 {
		unset(self::$cache[get_class($this).$this->getID()]);

		return self::$cachehandler->remove(
				get_class($this).$this->getID(), $this);
	 }
	/**
	 * Initialise the cachehandler.
	 *
	 * Sets a new cachehandler, choosing between memcache or filecache
	 * depending on killboard settings.
	 */
	private static function init()
	{
		if(defined('DB_USE_MEMCACHE') && DB_USE_MEMCACHE == true)
			self::$cachehandler = new CacheHandlerHashedMem();
		else self::$cachehandler = new CacheHandlerHashed();
	}

	/**
	 * Return the object's ID.
	 *
	 * This is used as a key to cache the object so must return a value
	 * without calling the cache.
	 *
	 * @return integer
	 */
	abstract protected function getID();
}