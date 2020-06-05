<?php

namespace Toolbox;

use PHPUnit\Framework\TestCase;
use Toolbox\Module\File\FileResolver;
use Toolbox\Module\Upload\UploadResolver;

class UploadTest extends TestCase
{
    public function testUpload()
    {
        $config = include __DIR__.'/config.php';
        $path = UploadResolver::resolveFromRequest($config['base']['config'], $config['base']['file'])->upload();
        $this->assertTrue(FileResolver::config($config['base']['config'])->has($path['path']));
    }

    public function testValidSize()
    {
        $config = include __DIR__.'/config.php';

        $this->assertFalse(UploadResolver::resolveFromRequest($config['validSize']['config'], $config['validSize']['file'])->isValidSize());
    }

    public function testValidMimes()
    {
        $config = include __DIR__.'/config.php';

        $this->assertFalse(UploadResolver::resolveFromRequest($config['validMimes']['config'], $config['validMimes']['file'])->isValidMime());
    }
}
