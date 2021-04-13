<?php

namespace floriankarsten\simplestaging;



class Dir extends \Kirby\Toolkit\Dir {

    // // lifted from https://github.com/getkirby/kirby/blob/38afb2d650f3764d23d732b23ae8f04ab9ffba69/src/Toolkit/Dir.php#L321
    // // because it had baked ignoring of '.htacess'
    public static function read(string $dir, array $ignore = null, bool $absolute = false): array
    {
        if (is_dir($dir) === false) {
            return [];
        }

        // create the ignore pattern
        $ignore = $ignore ?? static::$ignore;
        $ignore = array_merge($ignore, ['..']);

        // scan for all files and dirs
        $result = array_values((array)array_diff(scandir($dir), $ignore));

        // add absolute paths
        if ($absolute === true) {
            $result = array_map(function ($item) use ($dir) {
                return $dir . '/' . $item;
            }, $result);
        }

        return $result;
    }

}