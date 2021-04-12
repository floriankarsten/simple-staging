<?php
load([
    'floriankarsten\\simplestaging\\DeployLive' => __DIR__ . '/jobs/DeployLive.php',
]);

Kirby::plugin('floriankarsten/simplestaging', [
    'options' => [
        'jobs' => [ // you custom jobs
            'deploylive' => 'floriankarsten\\simplestaging\\DeployLive',
        ],
   ],
]);