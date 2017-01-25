<?php
// src/AppBundle/Serializer/OrganizationSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Organization\Organization;

class OrganizationSerializer extends AbstractSerializer
{
    static protected function getObjectName()
    {
        return 'organization';
    }

    static protected function getArrayName()
    {
        return 'organizations';
    }

    static protected function serialize(PropertiesInterface $organization = NULL)
    {
        return ( $organization instanceof Organization ) ? [
            $organization::PROPERTY_ID   => $organization->getId(),
            $organization::PROPERTY_NAME => $organization->getName(),
        ] : NULL;
    }
}
