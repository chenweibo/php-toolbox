<?php

namespace Toolbox\Tests;

use PHPUnit\Framework\TestCase;
use Toolbox\Module\File\FileResolver;

class FileTest extends TestCase
{
    public function testZip()
    {
        $config = include __DIR__ . '/config.php';
        $system = FileResolver::config($config['base']['config']);

        $this->assertFalse(false);
    }
}
