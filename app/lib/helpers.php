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

function get_bzfs_procs()
{
    exec("ps -ef|grep bzfs|grep -v grep", $output);

    $general_settings = config('general');
    $tmp = $general_settings['temp_path'];

    $regex_tmp = str_replace('/', '\/', $tmp);

    $procs = array();

    foreach ($output as $proc) {
        $uid = preg_replace("/^.+-pidfile " . $regex_tmp . "\/bzfs\.(\d\d\d\d)/u", "$1", $proc);

        $contents = file_get_contents($tmp . "/bzfs-scores." . $uid);
        $start = strrpos($contents, '#teams ');
        $scores = substr($contents, $start);

        // Find the last line that begins with 

        $procs[] = array(
            'uid' => $uid,
            'port' => preg_replace("/^.+ -p (\d*).+/u", "$1", $proc),
            'scores' => $scores,
        );

        
    }

    return $procs;
}