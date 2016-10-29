<?php
// src/AppBundle/Validator/Constraints/IsNfcTagCodeValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsNfcTagCodeValidator extends ConstraintValidator
{
    const NFC_TAG_CODE_PATTERN = '/^[a-z0-9]{1,32}$/';

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::NFC_TAG_CODE_PATTERN, $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
