<?php
// src/AppBundle/Entity/NfcTag/Properties/NfcTagPropertiesInterface.php
namespace AppBundle\Entity\NfcTag\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface NfcTagPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID     = 'id';
    const PROPERTY_NUMBER = 'number';
    const PROPERTY_CODE   = 'code';
}
