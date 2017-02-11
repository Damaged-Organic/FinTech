<?php
// src/AppBundle/Serializer/BanknoteSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Banknote\Banknote;

class BanknoteSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'banknote';
    }

    static public function getArrayName()
    {
        return 'banknotes';
    }

    protected function serialize(PropertiesInterface $banknote = NULL)
    {
        return ( $banknote instanceof Banknote ) ? [
            $banknote::PROPERTY_CURRENCY => $banknote->getCurrency(),
            $banknote::PROPERTY_NOMINAL  => $banknote->getNominal(),
        ] : NULL;
    }
}
