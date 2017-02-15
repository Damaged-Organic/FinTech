<?php
// src/AppBundle/Serializer/AccountGroupSerializer.php
namespace AppBundle\Serializer;

use BadMethodCallException;

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

    static public function getObjectName()
    {
        return 'account-group';
    }

    static public function getArrayName()
    {
        return 'account-groups';
    }

    protected function serialize(PropertiesInterface $accountGroup = NULL)
    {
        return ( $accountGroup instanceof AccountGroup ) ? [
            $accountGroup::PROPERTY_ID   => $accountGroup->getId(),
            $accountGroup::PROPERTY_NAME => $accountGroup->getName(),
        ] : FALSE;
    }

    protected function unserialize(array $serializedAccountGroup = NULL)
    {
        $accountGroup = new AccountGroup();

        $accountGroup
            ->setId(
                !empty($serializedAccountGroup[$accountGroup::PROPERTY_ID])
                    ? $serializedAccountGroup[$accountGroup::PROPERTY_ID]
                    : NULL
            )
            ->setName(
                !empty($serializedAccountGroup[$accountGroup::PROPERTY_NAME])
                    ? $serializedAccountGroup[$accountGroup::PROPERTY_NAME]
                    : NULL
            )
        ;

        return $accountGroup;
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

    public function syncUnserialize(array $serializedAccountGroup = NULL)
    {
        throw new BadMethodCallException('Not implemented!');
    }
}
