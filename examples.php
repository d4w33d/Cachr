<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(__file__));

set_include_path(implode(PATH_SEPARATOR, array(
    ROOT_DIR . DS . 'libs',
    get_include_path()
)));

spl_autoload_register(function($className) {
    @include_once str_replace('\\', DS, $className) . '.php';
});

use Cachr\Cachr;

$backendName = isset($_GET['b']) ? $_GET['b'] : null;
$currentUrl = basename(__file__) . '?b=' . $backendName;
$backend = null;
$cache = null;
$exception = null;

try {
    if ($backendName) {
        if ($backendName == 'file') {
            $backend = new \Cachr\Backends\File();
            $backend->setDirectory(ROOT_DIR . DS . 'cache');
        } else if ($backendName == 'memory') {
            $backend = new \Cachr\Backends\Memory();
        } else if ($backendName == 'memcache') {
            $backend = new \Cachr\Backends\Memcache();
            $backend->addServer('localhost', 11211);
        } else if ($backendName == 'memcached') {
            $backend = new \Cachr\Backends\Memcached();
            $backend->addServer('localhost', 11211);
        } else if ($backendName == 'apc') {
            $backend = new \Cachr\Backends\APC();
        }
        if ($backend) {
            $cache = Cachr::instance(Cachr::DEFAULT_INSTANCE, $backend);
        }
    }
} catch (\Exception $exception) {}

if (isset($_GET['delete'])) {
    $cache->delete('foo');
} else if (isset($_POST['value'])) {
    $ttl = isset($_POST['ttl']) ? $_POST['ttl'] : 0;
    $cache->set('foo', $_POST['value'], (int) $ttl);
    header('Location: ' . $currentUrl);
    exit;
}

$entry = $cache ? $cache->get('foo') : null;

include ROOT_DIR . DS . 'template.phtml';
