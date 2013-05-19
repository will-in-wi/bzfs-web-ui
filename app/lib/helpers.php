<?php

use Symfony\Component\Yaml\Yaml;

/**
 * Scan the api path, recursively including all PHP files
 *
 * @link https://gist.github.com/3438784
 * @param string  $dir
 * @param int     $depth      (optional)
 * @param array   $exceptions (optional) Files which are not to be loaded.
 */
function _require_all($dir, $depth=0, $exceptions = array()) {

    $app = \Slim\Slim::getInstance();
    $log = $app->getLog();

    if ($depth > 5) {
        return;
    }

    // require all php files
    $scan = glob("$dir/*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            $filename = explode('/', $path);
            if (!in_array(end($filename), $exceptions)) {
                require_once $path;
                // $log->debug("Loaded route " . $path);
            }
        }
        elseif (is_dir($path)) {
            _require_all($path, $depth+1);
        }
    }
}

function config($file)
{
    return Yaml::parse(APP_ROOT . '/app/config/' . $file . '.yml');
}