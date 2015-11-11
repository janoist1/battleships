<?php

namespace Worldstores\Battleships\Service\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Worldstores\Battleships\Service\GameService;

/**
 * Battleships game service integration for Silex.
 *
 * @package Worldstores\Battleships\Service\Provider
 */
class GameServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['game.settings'] = [];

        // inject the GameService
        $app['game'] = $app->share(function ($app) {
            return new GameService($app['game.settings']);
        });

    }

    public function boot(Application $app)
    {
    }
}
