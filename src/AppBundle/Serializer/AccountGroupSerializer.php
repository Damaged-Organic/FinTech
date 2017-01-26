<?php
// src/AppBundle/Serializer/AccountGroupSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Account\AccountGroup,
    AppBundle\Serializer\OrganizationSerializer,
    AppBundle\Serializer\AccountSerializer;

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

    static protected function syncSerialize(PropertiesInterface $accountGroup = NULL)
    {
        if( !($accountGroup instanceof AccountGroup) )
            return NULL;

        $serialized = static::serialize($accountGroup);

        $serialized = array_merge(
            $serialized,
            OrganizationSerializer::serializeObject($accountGroup->getOrganization())
        );

        $serialized = array_merge(
            $serialized,
            AccountSerializer::serializeArray($accountGroup->getAccounts())
        );

        return $serialized;
    }
}
