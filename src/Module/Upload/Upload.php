<?php

namespace Toolbox\Module\Upload;

use League\Flysystem\Adapter\Local;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Toolbox\Module\Support\Fluent;

class Upload
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $file;

    /**
     * @var array
     */
    protected $mimes = [];

    /**
     * @var int
     */
    protected $maxSize = 0;

    /**
     * return League\Flysystem\Filesystem.
     */
    private $filesystem;

    /**
     * @return array|mixed
     */
    public function getMimes()
    {
        return $this->mimes;
    }

    /**
     * @var string
     */
    protected $filenameType;

    /**
     * @param string $humanFileSize
     *
     * @return int
     */
    protected function filesize2bytes($humanFileSize)
    {
        $bytes = 0;

        $bytesUnits = [
            'K' => 1024,
            'M' => 1024 * 1024,
            'G' => 1024 * 1024 * 1024,
            'T' => 1024 * 1024 * 1024 * 1024,
            'P' => 1024 * 1024 * 1024 * 1024 * 1024,
        ];

        $bytes = floatval($humanFileSize);

        if (preg_match('~([KMGTP])$~si', rtrim($humanFileSize, 'B'), $matches) && !empty($bytesUnits[\strtoupper($matches[1])])) {
            $bytes *= $bytesUnits[\strtoupper($matches[1])];
        }

        return intval(round($bytes, 2));
    }

    /**
     * @return bool
     */
    public function isValidSize()
    {
        $maxSize = $this->filesize2bytes($this->maxSize);

        return $this->file['size'] <= $maxSize || 0 === $maxSize;
    }

    /**
     * @return bool
     */
    public function isValidMime()
    {
        return $this->mimes === ['*'] || \in_array($this->file['mime'], $this->mimes);
    }

    public function __construct(array $config, array $file)
    {
        $config = new Fluent($config);
        $this->file = $file;
        $this->maxSize = $config->get('max_size', 0);
        $this->mimes = $config->get('mimes');

        $adapter = new Local($config->get('root_path'));
        $filesystem = new Filesystem($adapter, ['visibility' => 'public']);
        $this->filesystem = $filesystem;

        $this->filenameType = $config->get('filename_type', 'md5_file');
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        switch ($this->filenameType) {
            case 'original':
                return $this->file['fileName'];
            case 'md5_file':
                return md5_file($this->file['realPath']) . '.' . $this->file['ext'];
                break;
            case 'random':
            default:
                return md5_file($this->file['realPath']) . '.' . $this->file['ext'];
        }
    }

    public function validate()
    {
        if (!$this->isValidMime($this->file['mime'])) {
            throw new \Exception(\sprintf('Invalid mime "%s".', $this->file['mime']));
        }
        if (!$this->isValidSize($this->file['size'])) {
            throw new \Exception(\sprintf('File has too large size("%s").', $this->file['size']));
        }
    }

    public function upload($toPath = '')
    {
        $path = \sprintf('%s/%s', \rtrim($toPath, '/'), $this->getFilename());

        $this->validate();

        $stream = fopen($this->file['realPath'], 'r+');
        $this->filesystem->put(
            $path,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }

        return ['path' => $path, 'size' => $this->file['size']];
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
}
