<?php
// src/AppBundle/Serializer/BankingMachineSerializer.php
namespace AppBundle\Serializer;

use BadMethodCallException;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Serializer\OrganizationSerializer;

class BankingMachineSerializer extends AbstractSyncSerializer
{
    private $_serializers = [];

    public function setOrganizationSerializer(OrganizationSerializer $organizationSerializer)
    {
        $this->_serializers[OrganizationSerializer::class] = $organizationSerializer;
    }

    static public function getObjectName()
    {
        return 'banking-machine';
    }

    static public function getArrayName()
    {
        return 'banking-machines';
    }

    protected function serialize(PropertiesInterface $bankingMachine = NULL)
    {
        return ( $bankingMachine instanceof BankingMachine ) ? [
            $bankingMachine::PROPERTY_ID       => $bankingMachine->getId(),
            $bankingMachine::PROPERTY_SERIAL   => $bankingMachine->getSerial(),
            $bankingMachine::PROPERTY_NAME     => $bankingMachine->getName(),
            $bankingMachine::PROPERTY_ADDRESS  => $bankingMachine->getAddress(),
            $bankingMachine::PROPERTY_LOCATION => $bankingMachine->getLocation(),
        ] : FALSE;
    }

    protected function unserialize(array $serializedBankingMachine = NULL)
    {
        $bankingMachine = new BankingMachine();

        $bankingMachine
            ->setId(
                !empty($serializedBankingMachine[$bankingMachine::PROPERTY_ID])
                    ? $serializedBankingMachine[$bankingMachine::PROPERTY_ID]
                    : NULL
            )
            ->setSerial(
                !empty($serializedBankingMachine[$bankingMachine::PROPERTY_SERIAL])
                    ? $serializedBankingMachine[$bankingMachine::PROPERTY_SERIAL]
                    : NULL
            )
            ->setName(
                !empty($serializedBankingMachine[$bankingMachine::PROPERTY_NAME])
                    ? $serializedBankingMachine[$bankingMachine::PROPERTY_NAME]
                    : NULL
            )
            ->setAddress(
                !empty($serializedBankingMachine[$bankingMachine::PROPERTY_ADDRESS])
                    ? $serializedBankingMachine[$bankingMachine::PROPERTY_ADDRESS]
                    : NULL
            )
            ->setLocation(
                !empty($serializedBankingMachine[$bankingMachine::PROPERTY_LOCATION])
                    ? $serializedBankingMachine[$bankingMachine::PROPERTY_LOCATION]
                    : NULL
            )
        ;

        return $bankingMachine;
    }

    protected function syncSerialize(PropertiesInterface $bankingMachine = NULL)
    {
        if( !($bankingMachine instanceof BankingMachine) )
            return NULL;

        $serialized = $this->serialize($bankingMachine);

        $serialized = array_merge(
            $serialized,
            $this->_serializers[OrganizationSerializer::class]->serializeObject($bankingMachine->getOrganization())
        );

        return $serialized;
    }

    public function syncUnserialize(array $serializedBankingMachine = NULL)
    {
        throw new BadMethodCallException('Not implemented!');
    }
}
