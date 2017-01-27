<?php
// src/AppBundle/Serializer/NfcTagSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\NfcTag\NfcTag;

class NfcTagSerializer extends AbstractSerializer
{
    static protected function getObjectName()
    {
        return 'nfc-tag';
    }

    static protected function getArrayName()
    {
        return 'nfc-tags';
    }

    protected function serialize(PropertiesInterface $nfcTag = NULL)
    {
        return ( $nfcTag instanceof NfcTag ) ? [
            $nfcTag::PROPERTY_ID     => $nfcTag->getId(),
            $nfcTag::PROPERTY_NUMBER => $nfcTag->getNumber(),
            $nfcTag::PROPERTY_CODE   => $nfcTag->getCode(),
        ] : NULL;
    }
}
