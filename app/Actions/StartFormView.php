<?php

namespace BzfsWebUi\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use \BzfsWebUi\Lib\ConfigParser;

class StartFormView {
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $good_flags = ConfigParser::config('good_flags');
        $bad_flags = ConfigParser::config('bad_flags');
        $maps = ConfigParser::config('maps');

        return $this->container->get('view')->render($response, 'start_server.html.twig', [
            'good_flags' => $good_flags,
            'bad_flags' => $bad_flags,
            'maps' => $maps,
        ]);
    }
}
