<?php
// src/AppBundle/Entity/Account/Properties/AccountGroupPropertiesInterface.php
namespace AppBundle\Entity\Account\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface AccountGroupPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID   = 'id';
    const PROPERTY_NAME = 'name';
}
