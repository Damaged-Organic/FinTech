<?php
// src/AppBundle/Serializer/BankingMachineEventSerializer.php
namespace AppBundle\Serializer;

use BadMethodCallException;

use DateTime;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\BankingMachine\BankingMachineEvent;

class BankingMachineEventSerializer extends AbstractSyncSerializer
{
    static public function getObjectName()
    {
        return 'event';
    }

    static public function getArrayName()
    {
        return 'events';
    }

    protected function serialize(PropertiesInterface $bankingMachineEvent = NULL)
    {
        return ( $bankingMachineEvent instanceof BankingMachineEvent ) ? [
            $bankingMachineEvent::PROPERTY_EVENT_AT => $bankingMachineEvent->getEventAt(),
            $bankingMachineEvent::PROPERTY_TYPE     => $bankingMachineEvent->getType(),
            $bankingMachineEvent::PROPERTY_CODE     => $bankingMachineEvent->getCode(),
            $bankingMachineEvent::PROPERTY_MESSAGE  => $bankingMachineEvent->getMessage(),
        ] : FALSE;
    }

    protected function unserialize(array $serializedBankingMachineEvent = NULL)
    {
        $bankingMachineEvent = new BankingMachineEvent();

        $bankingMachineEvent
            ->setEventAt(
                !empty($serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_EVENT_AT])
                    ? new DateTime($serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_EVENT_AT])
                    : NULL
            )
            ->setCode(
                !empty($serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_CODE])
                    ? $serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_CODE]
                    : NULL
            )
            ->setType(
                !empty($serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_TYPE])
                    ? $serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_TYPE]
                    : NULL
            )
            ->setMessage(
                !empty($serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_MESSAGE])
                    ? $serializedBankingMachineEvent[$bankingMachineEvent::PROPERTY_MESSAGE]
                    : NULL
            )
        ;

        return $bankingMachineEvent;
    }

    protected function syncSerialize(PropertiesInterface $bankingMachineEvent = NULL)
    {
        throw new BadMethodCallException('Not implemented!');
    }

    public function syncUnserialize(array $serializedBankingMachineEvent = NULL)
    {
        $bankingMachineEvent = $this->unserializeObject($serializedBankingMachineEvent);

        return $bankingMachineEvent;
    }
}
