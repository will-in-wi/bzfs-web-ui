<?php

namespace BzfsWebUi\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use \BzfsWebUi\Lib\ConfigParser;

class Management {
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $procs = $this->getBzfsProcs();

        return $this->container->get('view')->render($response, 'index.html.twig', [
            'procs' => $procs
        ]);
    }

    private function getBzfsProcs()
    {
        exec("ps -ef|grep bzfs|grep -v grep", $output);

        $general_settings = ConfigParser::config('general');
        $tmp = $general_settings['temp_path'];

        $regex_tmp = str_replace('/', '\/', $tmp);

        $procs = array();

        foreach ($output as $proc) {
            $uid = preg_replace("/^.+-pidfile " . $regex_tmp . "\/bzfs\.(\d\d\d\d)/u", "$1", $proc);

            $contents = file_get_contents($tmp . "/bzfs-scores." . $uid);
            $start = strrpos($contents, '#teams ');
            $scores = substr($contents, $start);

            $procs[] = array(
                'uid' => $uid,
                'port' => preg_replace("/^.+ -p (\d*).+/u", "$1", $proc),
                'scores' => $scores,
            );
        }

        return $procs;
    }
}
