<?php
load([
    'floriankarsten\\simplestaging\\DeployLive' => __DIR__ . '/src/DeployLive.php',
    'floriankarsten\\simplestaging\\Dir' => __DIR__ . '/src/Dir.php',
]);

Kirby::plugin('floriankarsten/simplestaging', [
    'options' => [
        'jobs' => [
            'deploylive' => 'floriankarsten\\simplestaging\\DeployLive',
        ],
   ],
]);