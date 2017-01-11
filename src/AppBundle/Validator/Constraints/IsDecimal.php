<?php
// AppBundle/Validator/Constraints/IsDecimal.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsDecimal extends Constraint
{
    public $message = "custom.decimal.valid";
}
