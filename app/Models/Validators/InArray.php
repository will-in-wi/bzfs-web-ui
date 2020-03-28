<?php

namespace BzfsWebUi\Models\Validators;

use BzfsWebUi\Models\ConfigForm;

class InArray implements iValidator {
    protected $allowedValues;

    public function __construct($allowedValues) {
        $this->allowedValues = $allowedValues;
    }

    public function isValid($value, ConfigForm $form) {
        return in_array($value, $this->allowedValues, true);
    }
}
