<?php

namespace Cachr\Backends;

use Cachr\BackendInterface;

class File implements BackendInterface
{

    private $dir;
    private $salt = '';

    public function setDirectory($dir)
    {
        $this->dir = $dir;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function computeFilename($key)
    {
        $ds = DIRECTORY_SEPARATOR;
        $baseName = sha1($this->salt . $key);
        return $this->dir . $ds . substr($baseName, 0, 4) . $ds . $baseName;
    }

    public function makeDirs($dir)
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = '';
        foreach (explode($ds, $dir) as $f)
        {
            $path .= ($path ? $ds : '') . $f;
            if (!$path)
            {
                $path = $ds;
            }
            if (!is_dir($path))
            {
                mkdir($path);
            }
        }
    }

    public function get($key)
    {
        $fileName = $this->computeFilename($key);
        if (!is_file($fileName))
        {
            return null;
        }
        $data = unserialize(file_get_contents($fileName));
        if ($data->expires !== 0 && $data->expires < time())
        {
            $this->delete($key);
            return null;
        }
        return $data->value;
    }

    public function set($key, $value, $ttl)
    {
        $fileName = $this->computeFilename($key);
        $dirName = dirname($fileName);
        if (!is_dir($dirName))
        {
            $this->makeDirs($dirName);
        }
        $data = new \stdClass();
        $data->value = $value;
        $data->expires = $ttl > 0 ? time() + $ttl : 0;
        file_put_contents($fileName, serialize($data));
    }

    public function delete($key)
    {
        $fileName = $this->computeFilename($key);
        if (!file_exists($fileName))
        {
            return;
        }
        unlink($fileName);

        $dirName = dirname($fileName);
        $od = opendir($dirName);
        while ($f = readdir($od))
        {
            if ($f != '.' && $f != '..')
            {
                closedir($od);
                return;
            }
        }
        closedir($od);
        rmdir($dirName);
    }

}
