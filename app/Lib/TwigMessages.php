<?php
// Ported from https://github.com/kanellov/slim-twig-flash/pull/7
// The actual code is too old.


/**
 * Slim Twig Flash.
 *
 * @link https://github.com/kanellov/slim-twig-flash for the canonical source repository
 *
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos <contact@kanellov.com>
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */
namespace BzfsWebUi\Lib;

use Slim\Flash\Messages;
use \Twig\Extension\AbstractExtension;
use \Twig\TwigFunction;

class TwigMessages extends AbstractExtension
{
    /**
     * @var Messages
     */
    protected $flash;

    /**
     * Constructor.
     *
     * @param Messages $flash the Flash messages service provider
     */
    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    /**
     * Extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'slim-twig-flash';
    }

    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('flash', [$this, 'getMessages']),
        ];
    }

    /**
     * Returns Flash messages; If key is provided then returns messages
     * for that key.
     *
     * @param string $key
     *
     * @return array
     */
    public function getMessages($key = null)
    {
        if (null !== $key) {
            return $this->flash->getMessage($key);
        }

        return $this->flash->getMessages();
    }
}
