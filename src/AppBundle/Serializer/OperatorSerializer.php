<?php
// src/AppBundle/Serializer/OperatorSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Operator\Operator,
    AppBundle\Entity\Operator\Properties\OperatorPropertiesInterface,
    AppBundle\Serializer\OperatorGroupSerializer,
    AppBundle\Serializer\NfcTagSerializer;

class OperatorSerializer extends AbstractSerializer impements SyncSerializerInterface
{
    static protected function getObjectName()
    {
        return 'operator';
    }

    static protected function getArrayName()
    {
        return 'operators';
    }

    static protected function serialize(OperatorPropertiesInterface $operator = NULL)
    {
        return ( $operator instanceof Operator ) ? [
            $operator::PROPERTY_ID        => $operator->getId(),
            $operator::PROPERTY_FULL_NAME => $operator->getFullName(),
        ] : NULL;
    }

    static public function serializeForSync(OperatorPropertiesInterface $operator = NULL)
    {
        if( !($operator instanceof Operator) )
            return NULL;

        $serialized = static::serialize($operator);

        $serialized = array_merge(
            $serialized,
            OperatorGroupSerializer::serializeSingle($operator->getOperatorGroup())
        );

        $serialized = array_merge(
            $serialized,
            NfcTagSerializer::serializeSingle($operator->getNfcTag())
        );

        // if( $organization = $operator->getOrganization() ) {
        //     $serialized['organization'] = [
        //         'id'   => $organization->getId(),
        //         'name' => $organization->getName(),
        //     ];
        // } else {
        //     $serialized['organization'] = NULL;
        // }
        //
        // if( $accountGroups = $operator->getAccountGroups() ) {
        //     foreach ($accountGroups as $accountGroup) {
        //         $serialized['account-groups'][] = [
        //             'id'   => $accountGroup->getId(),
        //             'name' => $accountGroup->getName()
        //         ];
        //     }
        // } else {
        //     $serialized['account-groups'] = NULL;
        // }

        return $serialized;
    }
}
