<?php
// src/AppBundle/Entity/Banknote/Properties/BanknoteListPropertiesInterface.php
namespace AppBundle\Entity\Banknote\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface BanknoteListPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_QUANTITY = 'quantity';
}
