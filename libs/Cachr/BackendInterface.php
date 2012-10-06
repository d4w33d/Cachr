<?php

namespace Cachr;

interface BackendInterface
{

    public function get($key);
    public function set($key, $value, $ttl);
    public function delete($key);

}
