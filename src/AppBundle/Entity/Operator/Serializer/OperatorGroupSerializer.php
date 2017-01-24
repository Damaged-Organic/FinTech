<?php
// src/AppBundle/Entity/Operator/Serializer/OperatorGroupSerializer.php
namespace AppBundle\Entity\Operator\Serializer;

use AppBundle\Entity\Operator\OperatorGroup;

class OperatorGroupSerializer extends \AppBundle\Entity\Utility\Extended\SyncSerializer
{
    static protected function serialize(OperatorGroup $operatorGroup = NULL)
    {
        return ( $operatorGroup ) ? [
            $operatorGroup::PROPERTY_ID   => $operatorGroup->getId(),
            $operatorGroup::PROPERTY_NAME => $operatorGroup->getName(),
            $operatorGroup::PROPERTY_ROLE => $operatorGroup->getRole(),
        ] : NULL;
    }
}
