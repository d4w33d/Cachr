<?php

namespace Cachr\Backends;

use Cachr\BackendInterface,
    Cachr\Exception;

class Memcached implements BackendInterface
{

    private $client;

    public function __construct()
    {
        if (!class_exists('\\Memcached'))
        {
            throw new Exception('PHP extension memcached is not available');
        }
        $this->client = new \Memcached();
    }

    public function addServer($host = 'localhost', $port = 11211)
    {
        if (!$this->client)
        {
            return;
        }
        $this->client->addServer($host, $port);
    }

    public function get($key)
    {
        if (!$this->client || ($value = $this->client->get($key)) === false)
        {
            return null;
        }
        return $value;
    }

    public function set($key, $value, $ttl)
    {
        if (!$this->client)
        {
            return;
        }
        $this->client->set($key, $value, time() + $ttl);
    }

    public function delete($key)
    {
        if (!$this->client)
        {
            return;
        }
        $this->client->delete($key);
    }

}
