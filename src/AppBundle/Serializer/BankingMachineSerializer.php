<?php
// src/AppBundle/Serializer/BankingMachineSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Serializer\OrganizationSerializer;

class BankingMachineSerializer extends AbstractSyncSerializer
{
    static protected function getObjectName()
    {
        return 'banking-machine';
    }

    static protected function getArrayName()
    {
        return 'banking-machines';
    }

    static protected function serialize(PropertiesInterface $bankingMachine = NULL)
    {
        return ( $bankingMachine instanceof BankingMachine ) ? [
            $bankingMachine::PROPERTY_ID       => $bankingMachine->getId(),
            $bankingMachine::PROPERTY_NAME     => $bankingMachine->getName(),
            $bankingMachine::PROPERTY_ADDRESS  => $bankingMachine->getAddress(),
            $bankingMachine::PROPERTY_LOCATION => $bankingMachine->getLocation(),
        ] : NULL;
    }

    static protected function syncSerialize(PropertiesInterface $bankingMachine = NULL)
    {
        if( !($bankingMachine instanceof BankingMachine) )
            return NULL;

        $serialized = static::serialize($bankingMachine);

        $serialized = array_merge(
            $serialized,
            OrganizationSerializer::serializeObject($bankingMachine->getOrganization())
        );

        return $serialized;
    }
}
