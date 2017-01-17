<?php
// src/AppBundle/Validator/Constraints/IsDeviceName.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsDeviceName extends Constraint
{
    public $message = "custom.device_name.valid";
}
