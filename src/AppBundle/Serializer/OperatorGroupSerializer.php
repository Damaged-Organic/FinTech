<?php
// src/AppBundle/Serializer/OperatorGroupSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Operator\OperatorGroup;

class OperatorGroupSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'operator-group';
    }

    static public function getArrayName()
    {
        return 'operator-groups';
    }

    protected function serialize(PropertiesInterface $operatorGroup = NULL)
    {
        return ( $operatorGroup instanceof OperatorGroup ) ? [
            $operatorGroup::PROPERTY_ID   => $operatorGroup->getId(),
            $operatorGroup::PROPERTY_NAME => $operatorGroup->getName(),
            $operatorGroup::PROPERTY_ROLE => $operatorGroup->getRole(),
        ] : NULL;
    }

    protected function unserialize(array $serializedOperatorGroup = NULL)
    {
        $operatorGroup = new OperatorGroup();

        $operatorGroup
            ->setId(
                !empty($serializedOperatorGroup[$operatorGroup::PROPERTY_ID])
                    ? $serializedOperatorGroup[$operatorGroup::PROPERTY_ID]
                    : NULL
            )
            ->setName(
                !empty($serializedOperatorGroup[$operatorGroup::PROPERTY_NAME])
                    ? $serializedOperatorGroup[$operatorGroup::PROPERTY_NAME]
                    : NULL
            )
            ->setRole(
                !empty($serializedOperatorGroup[$operatorGroup::PROPERTY_ROLE])
                    ? $serializedOperatorGroup[$operatorGroup::PROPERTY_ROLE]
                    : NULL
            )
        ;

        return $operatorGroup;
    }
}
