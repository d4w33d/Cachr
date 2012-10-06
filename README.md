# Cachr

Cachr is a caching library for PHP 5.3+.

Caching methods currently available are:

* Memory caching
* File caching
* Memcache
* Memcached
* APC (*Alternative PHP Cache*)

## Usage

See also **example.php**.

    <?php

    $backend = new \Cachr\Backends\File();
    $backend->setDirectory('/tmp/my_cache_dir');

    $cache = Cachr::instance(Cachr::DEFAULT_INSTANCE, $backend);

    $cache->set('foo', 'bar', 24 * 60 * 60);
    var_dump($cache->get('foo'));
    $cache->delete('foo');
