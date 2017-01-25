<?php
// src/AppBundle/Entity/Operator/Properties/OperatorPropertiesInterface.php
namespace AppBundle\Entity\Operator\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface OperatorPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID        = 'id';
    const PROPERTY_FULL_NAME = 'full-name';
}
