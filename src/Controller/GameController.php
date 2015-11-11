<?php

namespace Worldstores\Battleships\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Worldstores\Battleships\Exception\GameException;
use Worldstores\Battleships\Service\GameService;

/**
 * Class GameController
 * @package Worldstores\Battleships\Controller
 */
class GameController
{
    /** @var Application */
    private $app;

    /**
     * @param Application $app
     */
    function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Display home view
     *
     * @return string
     */
    public function home()
    {
        // load game session
        $this->loadGameSession();

        // get messages if there are any
        $message = implode(', ', $this->getFlashBag()->get('message', []));

        // show the ships
        $show = $this->getFlashBag()->has('show');

        // clear the flash messages
        $this->getFlashBag()->clear();

        return $this->getTwig()->render('game/home.html.twig', [
            'message' => $message,
            'show' => $show,
            'board' => $this->getGame()->getBoard()->buildMatrix(),
        ]);
    }

    /**
     * Handle turn request then redirect to home
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function turn(Request $request)
    {
        // get the coordinate from the request
        $coordinate = $request->get('coordinate');

        // load game session
        $this->loadGameSession();

        switch ($coordinate) {
            default:
                // turn
                $message = $this->doTurn($coordinate);
                break;

            case '';
                $message = 'enter a coordinate';
                break;

            case 'show':
                $this->getFlashBag()->set('show', '1');
                $message = 'showing ships';
                break;

            case 'reset':
                $this->resetGame();
                $message = 'game restarted';
                break;
        }

        // store message to display it next time
        $this->getFlashBag()->add('message', $message);

        // update session
        $this->updateGameSession();

        // redirect to home page
        return $this->app->redirect($this->getUrlGenerator()->generate('home'));
    }

    /**
     * Turning game
     * return status message
     *
     * @param $coordinate
     * @return string
     */
    private function doTurn($coordinate)
    {
        $game = $this->getGame();
        $coordinateTranslated = $this->translateCoordinates($coordinate);

        try {
            $hit = $game->turn($coordinateTranslated);

            // if hit then the ship might sunk, lets check it
            if ($hit && $game->isShipSunk($coordinateTranslated)) {
                // also the game might end
                if ($game->isGameOver()) {
                    // get statistics
                    $shots = $game->getNumberOfShots();
                    $message = 'Well done! You completed the game in ' . $shots . ' shots';

                    // reset the game
                    $this->resetGame();
                } else {
                    $message = 'sunk';
                }

            } else {
                $message = $hit ? 'hit ' . $coordinate : 'miss';
            }
        } catch (GameException $e) {
            $message = 'error: ' . $e->getMessage();
        }

        return $message;
    }

    /**
     * Translate coordinates from format 'A5' to array [1, 5]
     *
     * @param $coordinates
     * @return array
     */
    private function translateCoordinates($coordinates)
    {
        $x = substr($coordinates, 1);
        $y = substr($coordinates, 0, 1);

        return [$x, ord(strtoupper($y)) - ord('A') + 1];
    }

    /**
     * load game status from session
     */
    private function loadGameSession()
    {
        $gameData = $this->getSession()->get('gameData', null);
        $this->getGame()->initialize($gameData);
    }

    /**
     * load game status from session
     */
    private function updateGameSession()
    {
        $this->getSession()->set('gameData', $this->getGame()->getGameData());
    }

    /**
     * restart game
     */
    private function resetGame()
    {
        $this->getGame()->restart();
        $this->getSession()->remove('gameData');
    }

    /**
     * @return UrlGenerator
     */
    private function getUrlGenerator()
    {
        return $this->app['url_generator'];
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        return $this->app['session'];
    }

    /**
     * Shortcut to the Session's FlashBag
     *
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
     */
    private function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    /**
     * @return \Twig_Environment
     */
    private function getTwig()
    {
        return $this->app['twig'];
    }

    /**
     * @return GameService
     */
    private function getGame()
    {
        return $this->app['game'];
    }
}
