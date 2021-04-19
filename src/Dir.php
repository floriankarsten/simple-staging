<?php

namespace floriankarsten\simplestaging;


class Dir extends \Kirby\Toolkit\Dir {

	// changed from dir class because it had .htacess in it
    public static $ignore = [
        '.',
        '..',
        '.DS_Store',
        '.gitignore',
        '.git',
        '.svn',
        'Thumb.db',
        '@eaDir'
    ];

    // // lifted from https://github.com/getkirby/kirby/blob/38afb2d650f3764d23d732b23ae8f04ab9ffba69/src/Toolkit/Dir.php#L321
    // // because it had baked ignoring of '.htacess'
    public static function read(string $dir, array $ignore = null, bool $absolute = false): array
    {
        if (is_dir($dir) === false) {
            return [];
        }
        $ignore = $ignore ?? self::$ignore;
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

	public static function copy(string $dir, string $target, bool $recursive = true, array $ignore = []): bool
    {
        if (is_dir($dir) === false) {
            throw new Exception('The directory "' . $dir . '" does not exist');
        }

        if (is_dir($target) === true) {
            throw new Exception('The target directory "' . $target . '" exists');
        }

        if (static::make($target) !== true) {
            throw new Exception('The target directory "' . $target . '" could not be created');
        }

        foreach (self::read($dir) as $name) {
            $root = $dir . '/' . $name;

            if (in_array($root, $ignore) === true) {
                continue;
            }

            if (is_dir($root) === true) {
                if ($recursive === true) {
                    self::copy($root, $target . '/' . $name, true, $ignore);
                }
            } else {
                \Kirby\Toolkit\F::copy($root, $target . '/' . $name);
            }
        }

        return true;
    }
}