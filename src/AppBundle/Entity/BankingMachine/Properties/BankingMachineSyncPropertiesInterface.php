<?php
// src/AppBundle/Entity/BankingMachine/Properties/BankingMachineSyncPropertiesInterface.php
namespace AppBundle\Entity\BankingMachine\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface BankingMachineSyncPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_SYNC_ID   = 'id';
    const PROPERTY_SYNC_TYPE = 'type';
    const PROPERTY_SYNC_AT   = 'at';

    const PROPERTY_CHECKSUM = 'checksum';
    const PROPERTY_DATA     = 'data';
}
