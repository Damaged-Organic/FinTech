<?php
// AppBundle/Validator/Constraints/IsSkypeName.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsSkypeName extends Constraint
{
    public $message = "common.skype_name.valid";
}
