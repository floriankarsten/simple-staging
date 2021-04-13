<?php

namespace floriankarsten\simplestaging;

// use Kirby\Toolkit\Dir;
// use Kirby\Toolkit\F;


class DeployLive extends \Bnomei\JanitorJob
{
    /**
     * @return array
     */
    public function job(): array
    {


        if(!$siteFolder = option('floriankarsten.simplestaging.destination')) {
            return [
                'status' => 500,
                'label' => 'floriankarsten.simplestaging.destination is not set',
                'error' => 'floriankarsten.simplestaging.destination is not set',
            ];
        }
        // base is folder that holds whole website that should be duplicated. By default its parent of index/public folder
        if(option('floriankarsten.simplestaging.base')) {
            $base = option('floriankarsten.simplestaging.base');
        } else {
            $base = dirname(kirby()->root('index'));
        }
        // return [
        //     'status' => 500,
        //     'label' => $base,
        // ];
		$tmpDestination = dirname($base) . '/__staging_' . basename($base);
		$finalDestination = dirname($base) . '/' . $siteFolder;
        $toBeDeleted = dirname($base) . '/__tobedeleted_' . $siteFolder;



        if(Dir::exists($tmpDestination)) {
            return [
                'status' => 500,
                'label' => 'Temporary folder already exists',
                'error' => 'Temporary folder already exists',
            ];
        }
        if(Dir::isWritable($tmpDestination)) {
            return [
                'status' => 500,
                'label' => 'Temporary folder is not writable',
                'error' => 'Temporary folder is not writable',
            ];
        }
        // duplicate staging to temporary destination
        // ray('duplicate staging to temporary destination', $base, $tmpDestination);
        Dir::copy($base, $tmpDestination);

        // rename live site (final destination) to $toBeDeleted
        // ray('rename live site (final destination) to $toBeDeleted', $finalDestination, $toBeDeleted);
        Dir::move($finalDestination, $toBeDeleted);

        // rename temporary destination to final destination
        // ray('rename temporary destination to final destination',$tmpDestination, $finalDestination);
        Dir::move($tmpDestination, $finalDestination);

        // cleanup $toBeDeleted
        Dir::remove($toBeDeleted);

        return [
            'status' => 200,
            'label' =>  'Staging pushed live',
        ];
    }

    // // lifted from https://github.com/getkirby/kirby/blob/38afb2d650f3764d23d732b23ae8f04ab9ffba69/src/Toolkit/Dir.php#L321
    // // because it had baked ignoring of '.htacess'
    // public static function read(string $dir, array $ignore = null, bool $absolute = false): array
    // {
    //     if (is_dir($dir) === false) {
    //         return [];
    //     }

    //     // create the ignore pattern
    //     $ignore = $ignore ?? static::$ignore;
    //     $ignore = array_merge($ignore, ['.', '..']);

    //     // scan for all files and dirs
    //     $result = array_values((array)array_diff(scandir($dir), $ignore));

    //     // add absolute paths
    //     if ($absolute === true) {
    //         $result = array_map(function ($item) use ($dir) {
    //             return $dir . '/' . $item;
    //         }, $result);
    //     }

    //     return $result;
    // }
    // // lifted from https://github.com/getkirby/kirby/blob/38afb2d650f3764d23d732b23ae8f04ab9ffba69/src/Toolkit/Dir.php#L49
    // // because we need the copy function to use our read
    // // This could be probably better done by extending Toolkit\Dir but works for now
    // public static function copy(string $dir, string $target, bool $recursive = true, array $ignore = []): bool
    // {
    //     if (is_dir($dir) === false) {
    //         throw new Exception('The directory "' . $dir . '" does not exist');
    //     }

    //     if (is_dir($target) === true) {
    //         throw new Exception('The target directory "' . $target . '" exists');
    //     }

    //     if (Dir::make($target) !== true) {
    //         throw new Exception('The target directory "' . $target . '" could not be created');
    //     }

    //     foreach (self::read($dir) as $name) {
    //         $root = $dir . '/' . $name;

    //         if (in_array($root, $ignore) === true) {
    //             continue;
    //         }

    //         if (is_dir($root) === true) {
    //             if ($recursive === true) {
    //                 self::copy($root, $target . '/' . $name);
    //             }
    //         } else {
    //             F::copy($root, $target . '/' . $name);
    //         }
    //     }

    //     return true;
    // }
}