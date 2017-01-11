<?php
// src/AppBundle/Validator/Constraints/IsHumanName.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsHumanName extends Constraint
{
    public $message = "custom.human_name.valid";
}
