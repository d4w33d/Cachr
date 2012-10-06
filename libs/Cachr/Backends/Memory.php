<?php

namespace Cachr\Backends;

use Cachr\BackendInterface;

class Memory implements BackendInterface
{

    private $data = array();

    public function get($key)
    {
        if (!isset($this->data[$key]))
        {
            return null;
        }
        $data = $this->data[$key];
        if ($data['expires'] < time())
        {
            unset($this->data[$key]);
            return null;
        }
        return $data['value'];
    }

    public function set($key, $value, $ttl)
    {
        $this->data[$key] = array(
            'value' => $value,
            'expires' => time() + $ttl
        );
    }

    public function delete($key)
    {
        if (isset($this->data[$key]))
        {
            unset($this->data[$key]);
        }
    }

}
