<?php

namespace BzfsWebUi\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use \BzfsWebUi\Lib\ConfigParser;

/**
 * Generate the command line and execute it.
 */
class StartFormSubmit {
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args) {
        $generalSettings = ConfigParser::config('general');

        $formModel = new \BzfsWebUi\Models\ConfigForm($request->getParsedBody());

        if (!$formModel->isValid()) {
            // This is ugly.
            var_dump($formModel->errors);
            throw new \Exception('Failed to validate response');
        }

        $cmd = $formModel->toCommand();
        $cmd .= " -printscore";

        $tmp = $generalSettings['temp_path'];

        $uid = rand(1000, 9999);
        $pidFile = $tmp . "/bzfs." . $uid;
        $cmd .= " -pidfile " . $pidFile;

        $cmd .= " > " . $tmp . "/bzfs-scores." . $uid . " 2> " . $tmp . '/bzfs-error.' . $uid;

        $cmd = $generalSettings['bzflag_path'] . "/bzfs " . $cmd . " &";

        exec($cmd);

        // Give files some time to create.
        sleep(1);

        try {
            $error_output = file_get_contents($tmp . '/bzfs-error.' . $uid);
            $this->container->get('flash')->addMessage('launch_error', $error_output);
        } catch (\Exception $e) {
            $this->container->get('flash')->addMessage('launch_error_error', 'Somehow failed to get the error.');
        }

        // Collect pid
        try {
            $pid = (integer) file_get_contents($pidFile);
            $this->container->get('flash')->addMessage('pid_success', 'The PID is ' . $pid . '. The server is started!');
        } catch (\Exception $e) {
            $this->container->get('flash')->addMessage('pid_error', 'PID File Not Found. The server is probably not started.');
            $this->container->get('flash')->addMessage('command_line', $cmd);
        }

        return $response->withStatus(302)->withHeader('Location', '/');
    }
}
