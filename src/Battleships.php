<?php

namespace Worldstores\Battleships;

use Silex\Application;
use Worldstores\Battleships\Service\GameService;


/**
 * Class Battleship
 * @package Worldstores\Battleship
 */
class Battleships extends Application
{
    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $app = parent::__construct($values);

        $this->loadProviders();
        $this->loadRoutes();

        return $app;
    }

    /**
     * Tie components to the app - dep. inj.
     */
    private function loadProviders()
    {
        $providers = $this->load('providers');

        foreach ($providers as $provider) {
            $this->register($provider[0], $provider[1]);
        }
    }

    /**
     * Map routes to controllers
     */
    private function loadRoutes()
    {
        $routes = $this->load('routes');

        foreach ($routes as $prefix => $controller) {
            $this->mount($prefix, $controller);
        }
    }

    /**
     * Load a configuration file from the app folder
     *
     * @param string $config
     * @return mixed
     */
    private function load($config)
    {
        return require __DIR__ . "/Resources/config/" . $config . ".php";
    }
}
