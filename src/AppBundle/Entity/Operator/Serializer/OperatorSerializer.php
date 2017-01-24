<?php
// src/AppBundle/Entity/Operator/Serializer/OperatorSerializer.php
namespace AppBundle\Entity\Operator\Serializer;

use AppBundle\Entity\Operator\Operator,
    AppBundle\Entity\Operator\Serializer\OperatorGroupSerializer;

class OperatorSerializer extends \AppBundle\Entity\Utility\Extended\SyncSerializer
{
    static protected function serialize(Operator $operator = NULL)
    {
        return ( $operator ) ? [
            $operator::PROPERTY_ID        => $operator->getId(),
            $operator::PROPERTY_FULL_NAME => $operator->getFullName(),
        ] : NULL;
    }

    static public function serializeSync(Operator $operator)
    {
        $serialized = static::serialize($operator);

        $serialized = array_merge(
            $serialized, OperatorGroupSerializer::serializeSingle($operator->getOperatorGroup())
        );

        // if( $nfcTag = $operator->getNfcTag() ) {
        //     $serialized['nfc-tag'] = [
        //         'id'     => $nfcTag->getId(),
        //         'number' => $nfcTag->getNumber(),
        //         'code'   => $nfcTag->getCode(),
        //     ];
        // } else {
        //     $serialized['nfc-tag'] = NULL;
        // }
        //
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
