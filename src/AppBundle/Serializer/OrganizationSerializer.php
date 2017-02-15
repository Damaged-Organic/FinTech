<?php
// src/AppBundle/Serializer/OrganizationSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Organization\Organization;

class OrganizationSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'organization';
    }

    static public function getArrayName()
    {
        return 'organizations';
    }

    protected function serialize(PropertiesInterface $organization = NULL)
    {
        return ( $organization instanceof Organization ) ? [
            $organization::PROPERTY_ID        => $organization->getId(),
            $organization::PROPERTY_NAME      => $organization->getName(),
            $organization::PROPERTY_LOGO_FILE => $organization->getLogoFile(),
        ] : FALSE;
    }

    protected function unserialize(array $serializedOrganization = NULL)
    {
        $organization = new Organization();

        $organization
            ->setId(
                !empty($serializedOrganization[$organization::PROPERTY_ID])
                    ? $serializedOrganization[$organization::PROPERTY_ID]
                    : NULL
            )
            ->setName(
                !empty($serializedOrganization[$organization::PROPERTY_NAME])
                    ? $serializedOrganization[$organization::PROPERTY_NAME]
                    : NULL
            )
            ->setLogoFile(
                !empty($serializedOrganization[$organization::PROPERTY_LOGO_FILE])
                    ? $serializedOrganization[$organization::PROPERTY_LOGO_FILE]
                    : NULL
            )
        ;

        return $organization;
    }
}
