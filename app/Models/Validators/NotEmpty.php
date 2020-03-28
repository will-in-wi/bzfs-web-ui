<?php

namespace BzfsWebUi\Models\Validators;

use BzfsWebUi\Models\ConfigForm;

class NotEmpty implements iValidator {
    public function isValid($value, ConfigForm $form) {
        return $value && !empty($value);
    }
}
