<?php
// src/AppBundle/Validator/Constraints/IsNfcTagCode.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsNfcTagCode extends Constraint
{
    public $message = "nfc_tag.code.valid";
}
