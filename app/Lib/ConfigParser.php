<?php

namespace BzfsWebUi\Lib;

use Symfony\Component\Yaml\Yaml;

class ConfigParser {
    public function config($file) {
        return Yaml::parseFile(APP_ROOT . '/app/config/' . $file . '.yml');
    }
}
