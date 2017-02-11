<?php
// src/AppBundle/Entity/Transaction/Properties/TransactionPropertiesInterface.php
namespace AppBundle\Entity\Transaction\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface TransactionPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_TRANSACTION_AT = 'transaction-at';
}
