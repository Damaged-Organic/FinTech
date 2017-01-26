<?php
// src/AppBundle/Entity/Account/Properties/AccountPropertiesInterface.php
namespace AppBundle\Entity\Account\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface AccountPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID   = 'id';
    const PROPERTY_NAME = 'name';
}
