<?php
// src/AppBundle/Serializer/BanknoteListSerializer.php
namespace AppBundle\Serializer;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Banknote\BanknoteList,
    AppBundle\Entity\Banknote\Banknote,
    AppBundle\Serializer\BanknoteSerializer;

class BanknoteListSerializer extends AbstractSerializer
{
    private $_serializers = [];

    public function setBanknoteSerializer(BanknoteSerializer $banknoteSerializer)
    {
        $this->_serializers[BanknoteSerializer::class] = $banknoteSerializer;
    }

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
        if( !($banknoteList instanceof BanknoteList) )
            return NULL;

        $serialized = [
            $banknoteList::PROPERTY_QUANTITY => $banknoteList->getQuantity(),
        ];

        $serialized = array_merge(
            $serialized,
            $this->_serializers[BanknoteSerializer::class]->serializeObject(
                $banknoteList->getBanknote()
            )
        );

        return $serialized;
    }

    protected function unserialize(array $serializedBanknoteList = NULL)
    {
        $banknoteList = new BanknoteList();

        $banknoteSerializer = $this->_serializers[BanknoteSerializer::class];

        $banknoteList
            ->setBanknote(
                $banknoteSerializer->unserializeObject($serializedBanknoteList)
            )
            ->setQuantity(
                !empty($serializedBanknoteList[$banknoteList::PROPERTY_QUANTITY])
                    ? $serializedBanknoteList[$banknoteList::PROPERTY_QUANTITY]
                    : NULL
            )
        ;

        return $banknoteList;
    }
}
