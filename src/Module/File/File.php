<?php

namespace Toolbox\Module\File;

use League\Flysystem\Adapter\Local;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Toolbox\Module\Support\Fluent;

class File
{
    /**
     * return League\Flysystem\Filesystem.
     */
    private $filesystem;

    /**
     * return League\Flysystem\Adapter\Local.
     */
    private $adapterPath;

    public function __construct($config)
    {
        $config = new Fluent($config);
        $adapter = new Local($config->get('root_path'));
        $this->adapterPath = $config->get('root_path');
        $filesystem = new Filesystem($adapter, ['visibility' => 'public']);
        $this->filesystem = $filesystem;
    }

    /**
     * Rename Files.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool $response
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function rename($from, $to)
    {
        return $this->filesystem->rename($from, $to);
    }

    /**
     * Create Directories.
     *
     * @param string $path
     *
     * @return bool $response
     */
    public function createDir($path)
    {
        return $this->filesystem->createDir($path);
    }

    /**
     * Copy Files.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool $response
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function copy($from, $to)
    {
        return $this->filesystem->copy($from, $to);
    }

    /**
     * Delete Directories.
     *
     * @param string $path
     *
     * @return bool $response
     */
    public function deleteDir($path)
    {
        return $this->filesystem->deleteDir($path);
    }

    /**
     * Delete Files or Directories.
     *
     * @param string $path
     *
     * @return bool
     *
     * @throws FileNotFoundException
     */
    public function delete($path)
    {
        return $this->filesystem->delete($path);
    }

    /**
     * Get all files in a directory.
     *
     * @param string $path
     *
     * @return array
     */
    public function files($path)
    {
        $fileList = array_diff(\scandir($this->getFullPath($path)), ['..', '.']);

        return $this->handleFilesList($fileList);
    }

    /**
     * Get all files in a directory.
     *
     * @param string $path
     *
     * @return array
     * @throws FileNotFoundException
     */
    public function handleFilesList(array $list)
    {
        $files = [];
        foreach ($list as $v) {
            $files[] = ['name' => $v, 'mime' => \is_dir($this->getFullPath($v)) ? 'directory' : $this->mimeTypes($v), 'size' => \is_dir($this->getFullPath($v)) ? null : $this->size($v)];
        }

        return $files;
    }

    public function getFullPath($path)
    {
        if ($path) {
            return $this->adapterPath . '/' . $path;
        }

        return $this->adapterPath;
    }

    /**
     * Read Files.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function readFiles($path)
    {
        return $this->filesystem->read($path);
    }

    /**
     * Check if a file or directory exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->filesystem->has($path);
    }

    /**
     * Get MimeTypes.
     *
     * @param string $path
     *
     * @return string|false
     *
     * @throws FileNotFoundException
     */
    public function mimeTypes($path)
    {
        return $this->filesystem->getMimetype($path);
    }

    /**
     * Get File Sizes.
     *
     * @param string $path
     *
     * @return string|false
     *
     * @throws FileNotFoundException
     */
    public function size($path)
    {
        return $this->filesystem->getSize($path);
    }

    public function zip($path, $name)
    {
        $zip = new \ZipArchive();

        if ($this->mimeTypes('zip') == 'directory') {
        }
    }

    public function isValidZip($path)
    {
        if ($this->mimeTypes($path) != 'application/zip') {
            throw new \Exception(\sprintf('no support  ("%s").', $this->mimeTypes($path)));
        }
    }

    public function unzip($path, $to = '')
    {
        $this->isValidZip($path);
        if ($this->has($path)) {
            $zip = new \ZipArchive();
            if ($zip->open($this->adapterPath . '/' . $path) === true) {
                $zip->extractTo($this->adapterPath . '/' . $to);
                $zip->close();

                return true;
            }

            return false;
        }
    }
}
