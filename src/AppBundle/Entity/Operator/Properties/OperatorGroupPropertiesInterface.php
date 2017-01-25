<?php
// src/AppBundle/Entity/Operator/Properties/OperatorGroupPropertiesInterface.php
namespace AppBundle\Entity\Operator\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface OperatorGroupPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID   = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_ROLE = 'role';
}
