<?php

namespace Cachr;

use Cachr\BackendInterface;

class Cachr
{

    const DEFAULT_INSTANCE = 'default'; // Default instance name
    const DEFAULT_TTL = 0; // Infinite time-to-live

    private static $instances = array();

    /**
     * Returns a Cachr instance and create it if needed
     * @param optional String $name Name of instance, default DEFAULT_INSTANCE
     * @param optional BackendInterface $backend Instance of cache backend
     * @return BackendInterface
     */
    public static function instance($name = null, BackendInterface $backend = null)
    {
        if ($name === null)
        {
            $name = self::DEFAULT_INSTANCE;
        }
        if (!isset(self::$instances[$name]))
        {
            self::$instances[$name] = new Cachr($backend);
        }
        return self::$instances[$name];
    }

    private $backend; // Current Cachr backend
    private $namespace = ''; // Current namespace
    private $defaultTTL = self::DEFAULT_TTL; // Default TTL of Cachr instance
    private $keySeparator = ':'; // Separator between parts of namespace and keys

    /**
     * Cachr constructor
     * @param optional BackendInterface $backend Instance of cache backend
     */
    public function __construct(BackendInterface $backend = null)
    {
        if ($backend)
        {
            $this->setBackend($backend);
        }
    }

    /**
     * Set current cache backend
     * @param BackendInterface $backend
     * @return self
     */
    public function setBackend(BackendInterface $backend)
    {
        $this->backend = $backend;
        return $this;
    }

    /**
     * Set current namespace
     * @param String $ns
     * @return self
     */
    public function setNamespace($ns)
    {
        $this->backend = $backend;
        return $this;
    }

    /**
     * Set char separator between parts of namespace and keys
     * @param String $keySeparator
     * @return self
     */
    public function setKeySeparator($keySeparator)
    {
        $this->keySeparator = $keySeparator;
        return $this;
    }

    /**
     * Get value from cache backend
     * @param String $key Cache ID
     * @return mixed
     */
    public function get($key)
    {
        return $this->backend->get($this->computeKey($key));
    }

    /**
     * Set value of cache entry
     * @param String $key Cache ID
     * @param mixed $value Value
     * @param optional Integer $ttl Time-to-live of cache entry
     */
    public function set($key, $value, $ttl = null)
    {
        $this->backend->set($this->computeKey($key), $value, $ttl ?: $this->defaultTTL);
        return $this;
    }

    /**
     * Delete entry from cache
     * @param String $key Cache ID
     * @return self
     */
    public function delete($key)
    {
        $this->backend->delete($this->computeKey($key));
        return $this;
    }

    /**
     * Get value from $func result if cache ID is not set, then returns value
     * of cache entry
     * @param String $key Cache ID
     * @param Closure $func Closure executed if cache entry is not set
     * @param optional Integer $ttl Time-to-live of cache entry
     * @return mixed
     */
    public function getset($key, \Closure $func, $ttl = null)
    {
        $key = $this->computeKey($key);
        if (($value = $this->get($key)) === null)
        {
            $value = $func();
            $this->set($key, $value, $ttl ?: $this->defaultTTL);
        }
        return $value;
    }

    /**
     * Get namespace from this cache instance
     * @param String $key Name of namespace (prefix of cache ID)
     * @return Cachr
     */
    public function ns($key)
    {
        $cache = new Cachr();
        return $cache
            ->setBackend($this->backend)
            ->setKeySeparator($this->keySeparator)
            ->setNamespace($this->computeKey($key));
    }

    /**
     * Concat namespace and cache ID
     * @param String $key Cache ID
     * @return String
     */
    private function computeKey($key)
    {
        if ($this->namespace)
        {
            $key = $this->namespace . $this->keySeparator . $key;
        }
        return $key;
    }

}
