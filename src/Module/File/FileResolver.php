<?php

namespace Toolbox\Module\File;

class FileResolver
{
    public static function config($config)
    {
        return new File($config);
    }
}
