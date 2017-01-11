<?php
// AppBundle/Validator/Constraints/IsSkypeName.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsSkypeName extends Constraint
{
    public $message = "custom.skype_name.valid";
}
