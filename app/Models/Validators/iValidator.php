<?php

namespace BzfsWebUi\Models\Validators;

use BzfsWebUi\Models\ConfigForm;

interface iValidator {
    // TODO: Consider removing $form, if no one is using it.
    public function isValid($value, ConfigForm $form);
}
