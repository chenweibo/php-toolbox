<?php

namespace Toolbox\Module\Upload;

class UploadResolver
{
    public static function resolveFromRequest($config, $file)
    {
        return new Upload($config, $file);
    }
}
