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
    private $_serializers = [];

    public function setOrganizationSerializer(OrganizationSerializer $organizationSerializer)
    {
        $this->_serializers[OrganizationSerializer::class] = $organizationSerializer;
    }

    public function setAccountSerializer(AccountSerializer $accountSerializer)
    {
        $this->_serializers[AccountSerializer::class] = $accountSerializer;
    }

    static protected function getObjectName()
    {
        return 'account-group';
    }

    static protected function getArrayName()
    {
        return 'account-groups';
    }

    protected function serialize(PropertiesInterface $accountGroup = NULL)
    {
        return ( $accountGroup instanceof AccountGroup ) ? [
            $accountGroup::PROPERTY_ID   => $accountGroup->getId(),
            $accountGroup::PROPERTY_NAME => $accountGroup->getName(),
        ] : NULL;
    }

    protected function syncSerialize(PropertiesInterface $accountGroup = NULL)
    {
        if( !($accountGroup instanceof AccountGroup) )
            return NULL;

        $serialized = $this->serialize($accountGroup);

        $serialized = array_merge(
            $serialized,
            $this->_serializers[OrganizationSerializer::class]->serializeObject(
                $accountGroup->getOrganization()
            )
        );

        $serialized = array_merge(
            $serialized,
            $this->_serializers[AccountSerializer::class]->serializeArray(
                $accountGroup->getAccounts()
            )
        );

        return $serialized;
    }
}
