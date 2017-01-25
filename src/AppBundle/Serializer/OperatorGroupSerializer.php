<?php
// src/AppBundle/Serializer/OperatorGroupSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Operator\OperatorGroup;

class OperatorGroupSerializer extends AbstractSerializer
{
    static protected function getObjectName()
    {
        return 'operator-group';
    }

    static protected function getArrayName()
    {
        return 'operator-groups';
    }

    static protected function serialize(PropertiesInterface $operatorGroup = NULL)
    {
        return ( $operatorGroup instanceof OperatorGroup ) ? [
            $operatorGroup::PROPERTY_ID   => $operatorGroup->getId(),
            $operatorGroup::PROPERTY_NAME => $operatorGroup->getName(),
            $operatorGroup::PROPERTY_ROLE => $operatorGroup->getRole(),
        ] : NULL;
    }
}
