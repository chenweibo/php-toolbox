<?php

namespace PhpUtils;

use PHPUnit\Framework\TestCase;
use PhpUtils\Module\Upload\UploadResolver;

class UploadTest extends TestCase
{
    public function testUpload()
    {
        $file = ['realPath' => __DIR__.'/test-files/1.png', 'fileName' => '1', 'mime' => 'image/png', 'size' => 1024 * 1024 * 3, 'ext' => 'png'];

        $config = [
            'filename_type' => 'md5_file', 'root_path' => __DIR__.'/test-files', 'max_size' => '4m',
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'], ];
        UploadResolver::resolveFromRequest($config, $file)->upload();
    }

    public function testValidSize()
    {
        $file = ['realPath' => __DIR__.'/test-files/1.png', 'fileName' => '1', 'mimes' => 'image/png', 'size' => 1024 * 1024 * 20, 'ext' => 'png'];

        $config = [
            'filename_type' => 'md5_file', 'root_path' => __DIR__.'/test-files', 'max_size' => '10m',
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'], ];

        $this->assertFalse(UploadResolver::resolveFromRequest($config, $file)->isValidSize());
    }

    public function testValidMimes()
    {
        $file = ['realPath' => __DIR__.'/test-files/1.png', 'fileName' => '1', 'mime' => 'xxxx', 'size' => 1024 * 1024 * 20, 'ext' => 'png'];

        $config = [
            'filename_type' => 'md5_file', 'root_path' => __DIR__.'/test-files', 'max_size' => '10m',
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'], ];

        $this->assertFalse(UploadResolver::resolveFromRequest($config, $file)->isValidMime());
    }
}
