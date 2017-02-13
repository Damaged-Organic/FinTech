<?php
// src/AppBundle/Serializer/ReplenishmentSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Transaction\Replenishment,
    AppBundle\Serializer\OperatorSerializer,
    AppBundle\Serializer\AccountGroupSerializer,
    AppBundle\Serializer\BanknoteListSerializer,
    AppBundle\Serializer\BanknoteSerializer;

class ReplenishmentSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'replenishment';
    }

    static public function getArrayName()
    {
        return 'replenishments';
    }

    protected function serialize(PropertiesInterface $replenishment = NULL)
    {
        return ( $replenishment instanceof Replenishment ) ? [
            $replenishment::PROPERTY_TRANSACTION_AT => $replenishment->getTransactionAt(),
        ] : NULL;
    }

    protected function unserialize(array $serializedReplenishment = NULL)
    {
        $replenishment = new Replenishment();

        $replenishment
            ->setTransactionAt(
                !empty($serializedReplenishment[$replenishment::PROPERTY_TRANSACTION_AT])
                    ? $serializedReplenishment[$replenishment::PROPERTY_TRANSACTION_AT]
                    : NULL
            )
        ;

        return $replenishment;
    }

    public function syncUnserialize(array $serializedReplenishment = NULL)
    {
        $replenishment = $this->unserializeObject($serializedReplenishment);

        

        return $replenishment;
    }
}
