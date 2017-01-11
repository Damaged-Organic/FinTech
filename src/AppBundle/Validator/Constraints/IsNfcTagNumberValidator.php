<?php
// src/AppBundle/Validator/Constraints/IsNfcTagNumberValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsNfcTagNumberValidator extends ConstraintValidator
{
    const NFC_TAG_NUMBER_PATTERN = '/^([A-Z]{2})?[0-9]{6}$/';

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::NFC_TAG_NUMBER_PATTERN, $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
