<?php

namespace floriankarsten\simplestaging;

class DeployLiveRsync extends \Bnomei\JanitorJob
{
    /**
     * @return array
     */

    static $defaultFlags = [
        'a', // --archive, -a            archive mode; equals -rlptgoD (no -H,-A,-X)
        'r', // --recursive, -r          recurse into directories
    ];

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

        $finalDestination = dirname($base) . '/' . $siteFolder;


        // check if we have rsync
        if(option('floriankarsten.simplestaging.rsync.executable')) {
            $exe = option('floriankarsten.simplestaging.rsync.executable');
        } else {
            $exe = 'rsync';
        }

        // watch out it means you need to have permisision to run which
        if(!self::verifyCommand($exe)) {
            return [
                'status' => 500,
                'label' =>  "Couldn't find rsync executable",
            ];
        }

        // build rsync command

        $command = [];

        $command[] = $exe;

        if(option('floriankarsten.simplestaging.rsync.flags')) {
            $flags = option('floriankarsten.simplestaging.rsync.flags');
        } else {
            $flags = self::$defaultFlags;
        }
        $flags = "-" . implode("", $flags);

        $command[] = $flags;


        // delete in destination default true
        if(option('floriankarsten.simplestaging.rsync.delete') !== null) {
            if(option('floriankarsten.simplestaging.rsync.delete')) {
                $command[] = "--delete";
            }
        } else {
            $command[] = "--delete";
        }

        // include files
        if(option('floriankarsten.simplestaging.rsync.include')) {
            // $command[] = "--include={'". implode("','", option('floriankarsten.simplestaging.rsync.include')) ."'}";
            $command[] = "--include '" . implode("' --include='", option('floriankarsten.simplestaging.rsync.include')) . "'";
        }

        // exclude files
        if(option('floriankarsten.simplestaging.rsync.exclude')) {
            // $command[] = "--exclude={'". implode("','", option('floriankarsten.simplestaging.rsync.exclude')) ."'}";
            $command[] = "--exclude '" . implode("' --exclude='", option('floriankarsten.simplestaging.rsync.exclude')) . "'";
        }

        if(option('floriankarsten.simplestaging.rsync.include') && !option('floriankarsten.simplestaging.rsync.exclude')) {
            $command[] = "--exclude={'*'}";
        }

        // files we get
        $command[] = $base . "/*";

        // destination
        $command[] = $finalDestination;



        // build command
        $command = implode(" ", $command);


        ray($command);
        // we can do it here
        exec($command, $output, $exit_code);

        if($exit_code === 0) {
            return [
                'status' => 200,
                'label' =>  'Staging pushed live',
            ];
        } elseif($exit_code === 23) {
            return [
                'status' => 500,
                'label' => 'rsync error: Partial transfer due to error',
                'error' => 'rsync error: Partial transfer due to error',
            ];
        } else {
            // fallback all other errors
            return [
                'status' => 500,
                'label' => 'rsync error:'. $exit_code . ' exit code',
                'error' => 'rsync error:'. $exit_code . ' exit code',
            ];
        }
        return [
            'status' => 500,
            'label' =>  'We got to bottom without success',
        ];
    }

    static function verifyCommand($command) :bool {
        $windows = strpos(PHP_OS, 'WIN') === 0;
        $test = $windows ? 'where' : 'command -v';
        return is_executable(trim(exec("$test $command")));
    }

}