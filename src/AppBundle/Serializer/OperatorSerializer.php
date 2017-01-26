<?php
// src/AppBundle/Serializer/OperatorSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Operator\Operator,
    AppBundle\Serializer\OperatorGroupSerializer,
    AppBundle\Serializer\NfcTagSerializer,
    AppBundle\Serializer\OrganizationSerializer,
    AppBundle\Serializer\AccountGroupSerializer;

class OperatorSerializer extends AbstractSyncSerializer
{
    static protected function getObjectName()
    {
        return 'operator';
    }

    static protected function getArrayName()
    {
        return 'operators';
    }

    static protected function serialize(PropertiesInterface $operator = NULL)
    {
        return ( $operator instanceof Operator ) ? [
            $operator::PROPERTY_ID        => $operator->getId(),
            $operator::PROPERTY_FULL_NAME => $operator->getFullName(),
        ] : NULL;
    }

    static protected function syncSerialize(PropertiesInterface $operator = NULL)
    {
        if( !($operator instanceof Operator) )
            return NULL;

        $serialized = static::serialize($operator);

        $serialized = array_merge(
            $serialized,
            OperatorGroupSerializer::serializeObject($operator->getOperatorGroup())
        );

        $serialized = array_merge(
            $serialized,
            OrganizationSerializer::serializeObject($operator->getOrganization())
        );

        $serialized = array_merge(
            $serialized,
            NfcTagSerializer::serializeObject($operator->getNfcTag())
        );

        $serialized = array_merge(
            $serialized,
            AccountGroupSerializer::serializeArray($operator->getAccountGroups())
        );

        return $serialized;
    }
}
