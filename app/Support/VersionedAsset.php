<?php

namespace App\Support;

use RuntimeException;

class VersionedAsset
{
    public static function url(string $path): string
    {
        $file = public_path($path);
        if (!is_file($file)) {
            throw new RuntimeException('Public asset is missing: '.$path);
        }
        $hash = hash_file('sha256', $file);
        if ($hash === false) {
            throw new RuntimeException('Public asset could not be hashed: '.$path);
        }

        return asset($path).'?v='.$hash;
    }
}
