<?php
// src/AppBundle/Entity/BankingMachine/Properties/BankingMachinePropertiesInterface.php
namespace AppBundle\Entity\BankingMachine\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface BankingMachinePropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID       = 'id';
    const PROPERTY_SERIAL   = 'serial';
    const PROPERTY_NAME     = 'name';
    const PROPERTY_ADDRESS  = 'address';
    const PROPERTY_LOCATION = 'location';
}
