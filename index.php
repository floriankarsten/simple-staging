<?php
load([
    'floriankarsten\\simplestaging\\DeployLive' => __DIR__ . '/jobs/DeployLive.php',
]);

Kirby::plugin('floriankarsten/simplestaging', [
    'options' => [
        'jobs' => [
            'deploylive' => 'floriankarsten\\simplestaging\\DeployLive',
        ],
   ],
]);