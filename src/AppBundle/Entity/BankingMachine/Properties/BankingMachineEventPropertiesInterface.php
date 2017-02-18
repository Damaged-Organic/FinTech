<?php
// src/AppBundle/Entity/BankingMachine/Properties/BankingMachineEventPropertiesInterface.php
namespace AppBundle\Entity\BankingMachine\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface BankingMachineEventPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_EVENT_AT = 'event-at';
    const PROPERTY_TYPE     = 'type';
    const PROPERTY_CODE     = 'code';
    const PROPERTY_MESSAGE  = 'message';
}
