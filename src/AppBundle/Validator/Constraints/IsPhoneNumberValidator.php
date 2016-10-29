<?php
// src/AppBundle/Validator/Constraints/IsPhoneNumberValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsPhoneNumberValidator extends ConstraintValidator
{
    const PHONE_NUMBER_PATTERN = '/^\+38\s\(0[0-9]{2}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/';

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::PHONE_NUMBER_PATTERN, $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
