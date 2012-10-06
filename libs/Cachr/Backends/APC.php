<?php

namespace Cachr\Backends;

use Cachr\BackendInterface;

class APC implements BackendInterface
{

    public function get($key)
    {
        if (($value = apc_fetch($key)) === false)
        {
            return null;
        }
        return unserialize($value);
    }

    public function set($key, $value, $ttl)
    {
        apc_store($key, serialize($value), $ttl);
    }

    public function delete($key)
    {
        apc_delete($key);
    }

}
