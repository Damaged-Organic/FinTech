<?php
// src/AppBundle/Validator/Constraints/IsDeviceNameValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsDeviceNameValidator extends ConstraintValidator
{
    const DEVICE_NAME_PATTERN = '/^[a-zA-Z0-9\p{L}_\-\s]+$/u';

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::DEVICE_NAME_PATTERN, $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
