<?php

namespace Worldstores\Battleships\Controller\Provider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Worldstores\Battleships\Controller\GameController;

/**
 * Class GameControllerProvider
 * @package Worldstores\Battleship\Controller\Provider
 */
class GameControllerProvider implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(Application $app)
    {
        $gameController = new GameController($app);

        /** @var ControllerCollection $controllerCollection */
        $controllerCollection = $app["controllers_factory"];

        // basic sub-routing with route names
        $controllerCollection
            ->get("/", [$gameController, 'home'])
            ->bind('home');
        $controllerCollection
            ->post("/", [$gameController, 'turn'])
            ->bind('turn');

        // set layout
        $app->before(function () use ($app) {
            $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout.html.twig'));
        });

        // add alphabet Twig filter
        $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig) {
            $twig->addFilter(new \Twig_SimpleFilter('alphabet', function ($i) {
                return chr(ord('A') + $i - 1);
            }));
            return $twig;
        }));

        return $controllerCollection;
    }
} 