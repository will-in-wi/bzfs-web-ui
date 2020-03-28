<?php

namespace BzfsWebUi\Models\Validators;

use BzfsWebUi\Models\ConfigForm;

class IntInRange implements iValidator {
    /** @var int */
    protected $min;
    /** @var int */
    protected $max;
    /** @var bool */
    protected $allow_empty;

    public function __construct(int $min = null, int $max = null, bool $allow_empty = false) {
        $this->min = $min;
        $this->max = $max;
        $this->allow_empty = $allow_empty;
    }

    public function isValid($value, ConfigForm $form) {
        if ($this->allow_empty && empty($value)) {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false &&
                ($this->min === null || $value >= $this->min) &&
                ($this->$max === null || $value <= $this->max);
    }
}
