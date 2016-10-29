<?php
// src/AppBundle/Validator/Constraints/IsHumanNameValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsHumanNameValidator extends ConstraintValidator
{
    const HUMAN_NAME_PATTERN = '/^[a-zA-Z\p{L}-’`]+$/u';

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::HUMAN_NAME_PATTERN, $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
