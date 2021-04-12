<?php

namespace floriankarsten\simplestaging;

use Kirby\Toolkit\Dir;


class DeployLive extends \Bnomei\JanitorJob
{
    /**
     * @return array
     */
    public function job(): array
    {
        ray('come on');
        if(!$siteFolder = option('floriankarsten.simplestaging.destination')) {
            return [
                'status' => 500,
                'label' => 'floriankarsten.simplestaging.destination is not set',
                'error' => 'floriankarsten.simplestaging.destination is not set',
            ];
        }
		$base = kirby()->root('base');
		$tmpDestination = dirname($base) . '/__staging_' . basename($base);
		$finalDestination = dirname($base) . '/' . $siteFolder;
        $toBeDeleted = dirname($base) . '/__tobedeleted_' . $siteFolder;

        ray($finalDestination);

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
        ray('duplicate staging to temporary destination', $base, $tmpDestination);
        Dir::copy($base, $tmpDestination);

        // rename live site (final destination) to $toBeDeleted
        ray('rename live site (final destination) to $toBeDeleted', $finalDestination, $toBeDeleted);
        Dir::move($finalDestination, $toBeDeleted);

        // rename temporary destination to final destination
        ray('rename temporary destination to final destination',$tmpDestination, $finalDestination);
        Dir::move($tmpDestination, $finalDestination);

        // cleanup $toBeDeleted
        Dir::remove($toBeDeleted);

        return [
            'status' => 200,
            'label' =>  'Staging pushed live',
        ];
    }
}