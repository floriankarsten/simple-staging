<?php


Kirby::plugin('floriankarsten/merkur', [
    'hooks' => [
        'system.loadPlugins:after' => function () use ($blueprintsRegistry, $templatesRegistry,$pageModels, $snippetsRegistry) {

        }
    ]
]);