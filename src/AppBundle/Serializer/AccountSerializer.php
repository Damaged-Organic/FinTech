<?php
// src/AppBundle/Serializer/AccountSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Account\Account;

class AccountSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'account';
    }

    static public function getArrayName()
    {
        return 'accounts';
    }

    protected function serialize(PropertiesInterface $account = NULL)
    {
        return ( $account instanceof Account ) ? [
            $account::PROPERTY_ID   => $account->getId(),
            $account::PROPERTY_NAME => $account->getName(),
        ] : FALSE;
    }

    protected function unserialize(array $serializedAccount = NULL)
    {
        $account = new Account();

        $account
            ->setId(
                !empty($serializedAccount[$account::PROPERTY_ID])
                    ? $serializedAccount[$account::PROPERTY_ID]
                    : NULL
            )
            ->setName(
                !empty($serializedAccount[$account::PROPERTY_NAME])
                    ? $serializedAccount[$account::PROPERTY_NAME]
                    : NULL
            )
        ;

        return $account;
    }
}
