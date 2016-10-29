<?php
// AppBundle/Validator/Constraints/IsDecimalValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsDecimalValidator extends ConstraintValidator
{
    const DECIMAL_PATTERN = '/^[0-9]{0,9}(?:(?:\.|\,)[0-9]{0,2})?$/';

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::DECIMAL_PATTERN, $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
