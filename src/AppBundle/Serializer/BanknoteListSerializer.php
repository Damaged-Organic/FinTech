<?php
// src/AppBundle/Serializer/BanknoteListSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Banknote\BanknoteList;

class BanknoteListSerializer extends AbstractSerializer
{
    static public function getObjectName()
    {
        return 'banknote-list';
    }

    static public function getArrayName()
    {
        return 'banknote-lists';
    }

    protected function serialize(PropertiesInterface $banknoteList = NULL)
    {
        return ( $banknoteList instanceof BanknoteList ) ? [
            $banknoteList::PROPERTY_QUANTITY => $banknoteList->getQuantity(),
        ] : NULL;
    }

    protected function unserialize(array $serializedBanknoteList = NULL)
    {
        $banknoteList = new BanknoteList();

        $banknoteList
            ->setQuantity(
                !empty($serializedBanknoteList[$banknoteList::PROPERTY_QUANTITY])
                    ? $serializedBanknoteList[$banknoteList::PROPERTY_QUANTITY]
                    : NULL
            )
        ;

        return $banknoteList;
    }
}
