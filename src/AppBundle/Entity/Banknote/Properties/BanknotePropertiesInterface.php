<?php
// src/AppBundle/Entity/Banknote/Properties/BanknotePropertiesInterface.php
namespace AppBundle\Entity\Banknote\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface BanknotePropertiesInterface extends PropertiesInterface
{
    const PROPERTY_CURRENCY = 'currency';
    const PROPERTY_NOMINAL  = 'nominal';
}
