<?php

namespace Worldstores\Battleships\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpKernel\Client;
use Worldstores\Battleships\Battleships;
use Worldstores\Battleships\Model\Point;
use Worldstores\Battleships\Model\Ship;
use Worldstores\Battleships\Service\GameService;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    /** @var Battleships */
    private $app;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->app = new Battleships();
        $this->app['session.test'] = true;

        $this->setMink(new Mink([
            'silex' => new Session(new BrowserKitDriver(new Client($this->app))),
//            'selenium' => new Session(new Selenium2Driver('firefox', null, 'http://10.0.2.2:4444/wd/hub')),
        ]));

        $this->getMink()->setDefaultSessionName('silex');
    }

    /**
     * Set size of the board
     *
     * @Given there is a :width x :height sized board
     *
     * @param $width
     * @param $height
     */
    public function thereIsABoard($width, $height)
    {
        $this->getGame()->getBoard()->setDimension(new Point($width, $height));
        $this->saveGameData();
    }

    /**
     * Add a ship onto the board with a given position
     *
     * @Given there is a :size length :orientation oriented ship with its first coordinate :coordinate
     *
     * @param $size
     * @param $orientation
     * @param $coordinate
     * @throws \Exception
     */
    public function thereIsALengthOrientedShipWithItsFirstCoordinate($size, $orientation, $coordinate)
    {
        $pointA = $this->coordinateToPoint($coordinate);

        switch ($orientation) {
            case 'horizontal';
                $pointB = new Point($pointA->getX() + $size - 1, $pointA->getY());
                break;
            case 'vertical';
                $pointB = new Point($pointA->getX(), $pointA->getY() + $size - 1);
                break;
            default:
                throw new \Exception('unknown ship orientation');
        }

        $this->getGame()->getBoard()->addShip(new Ship($pointA, $pointB, $size));
        $this->saveGameData();
    }

    /**
     * Shoot to a coordinate
     *
     * @Then I shoot to :coordinate
     *
     * @param $coordinate
     */
    public function thenIShootTo($coordinate)
    {
        $this->fillField("coordinate", $coordinate);
        $this->pressButton("submit");
    }

    /**
     * Shoot to a the given coordinates
     *
     * @Given /^the following shots$/
     *
     * @param TableNode $shots
     */
    public function givenTheFollowingShots(TableNode $shots)
    {
        foreach ($shots->getHash() as $shot) {
            $this->thenIShootTo($shot['coordinate']);
        }
    }

    /**
     * Dump content
     *
     * @Then I dump
     */
    public function thenDump()
    {
//        echo $this->getMink()->getSession('silex')->getDriver()->getContent() ."\n";
    }

    /**
     * Store the game data in the game's session
     */
    private function saveGameData()
    {
        $gameData = $this->getGame()->getGameData();
        $this->getHttpSession()->set('gameData', $gameData);
    }

    /**
     * Turn coordinate "A5" to Point
     *
     * @param $coordinate
     * @return Point
     */
    private function coordinateToPoint($coordinate)
    {
        $x = substr($coordinate, 1) * 1;
        $y = ord(strtoupper(substr($coordinate, 0, 1))) - ord('A') + 1;

        return new Point($x, $y);
    }

    /**
     * @return GameService
     */
    private function getGame()
    {
        return $this->app['game'];
    }

    /**
     * @return HttpSession
     */
    private function getHttpSession()
    {
        return $this->app['session'];
    }
}
