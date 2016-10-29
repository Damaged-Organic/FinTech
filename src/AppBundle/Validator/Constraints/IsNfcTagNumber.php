<?php
// src/AppBundle/Validator/Constraints/IsNfcTagNumber.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsNfcTagNumber extends Constraint
{
    public $message = "nfc_tag.number.valid";
}
