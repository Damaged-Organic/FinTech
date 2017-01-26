<?php
// src/AppBundle/Serializer/AccountSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Account\Account;

class AccountSerializer extends AbstractSerializer
{
    static protected function getObjectName()
    {
        return 'account';
    }

    static protected function getArrayName()
    {
        return 'accounts';
    }

    static protected function serialize(PropertiesInterface $account = NULL)
    {
        return ( $account instanceof Account ) ? [
            $account::PROPERTY_ID   => $account->getId(),
            $account::PROPERTY_NAME => $account->getName(),
        ] : NULL;
    }
}
