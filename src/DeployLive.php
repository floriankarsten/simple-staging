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
        $exclude = [];

        // ray(option('floriankarsten.simplestaging.exclude'))->die();
        // if(!option('floriankarsten.simplestaging.exclude')) {
            foreach(option('floriankarsten.simplestaging.exclude', []) as $folder) {
                $exclude[] = $base . "/" . $folder;
            }

        // }



        // duplicate staging to temporary destination
        // ray('duplicate staging to temporary destination', $base, $tmpDestination);
        Dir::copy($base, $tmpDestination, true, $exclude);

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

}