<?php
// AppBundle/Validator/Constraints/IsDecimalConstraintValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsDecimalConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match('/^[0-9]{0,9}(?:(?:\.|\,)[0-9]{0,2})?$/', $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}