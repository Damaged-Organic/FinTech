<?php
// src/AppBundle/Serializer/NfcTagSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\NfcTag\NfcTag;

class NfcTagSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'nfc-tag';
    }

    static public function getArrayName()
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

    protected function unserialize(array $serializedNfcTag = NULL)
    {
        $nfcTag = new NfcTag();

        $nfcTag
            ->setId(
                !empty($serializedNfcTag[$nfcTag::PROPERTY_ID])
                    ? $serializedNfcTag[$nfcTag::PROPERTY_ID]
                    : NULL
            )
            ->setNumber(
                !empty($serializedNfcTag[$nfcTag::PROPERTY_NUMBER])
                    ? $serializedNfcTag[$nfcTag::PROPERTY_NUMBER]
                    : NULL
            )
            ->setCode(
                !empty($serializedNfcTag[$nfcTag::PROPERTY_CODE])
                    ? $serializedNfcTag[$nfcTag::PROPERTY_CODE]
                    : NULL
            )
        ;

        return $nfcTag;
    }
}
