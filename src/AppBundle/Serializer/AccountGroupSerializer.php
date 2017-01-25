<?php
// src/AppBundle/Serializer/AccountGroupSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Account\AccountGroup;

class AccountGroupSerializer extends AbstractSyncSerializer
{
    static protected function getObjectName()
    {
        return 'account-group';
    }

    static protected function getArrayName()
    {
        return 'account-groups';
    }

    static protected function serialize(PropertiesInterface $accountGroup = NULL)
    {
        return ( $accountGroup instanceof AccountGroup ) ? [
            $accountGroup::PROPERTY_ID   => $accountGroup->getId(),
            $accountGroup::PROPERTY_NAME => $accountGroup->getName(),
        ] : NULL;
    }
}
