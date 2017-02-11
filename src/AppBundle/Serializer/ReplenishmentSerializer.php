<?php
// src/AppBundle/Serializer/ReplenishmentSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Transaction\Replenishment;

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
}
