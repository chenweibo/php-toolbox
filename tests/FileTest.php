<?php

namespace Toolbox\Tests;

use PHPUnit\Framework\TestCase;
use Toolbox\Module\File\FileResolver;

class FileTest extends TestCase
{
    public function testUnzip()
    {
        $config = include __DIR__.'/config.php';
        $system = FileResolver::config($config['base']['config']);

        $this->assertTrue($system->unzip('un.zip', 'unzip'));
    }

    public function testFiles()
    {
        $config = include __DIR__.'/config.php';
        $system = FileResolver::config($config['base']['config'])->files('');
        //var_dump($system);
        $this->assertTrue(true);
    }
}
