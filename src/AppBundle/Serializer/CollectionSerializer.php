<?php
// src/AppBundle/Serializer/CollectionSerializer.php
namespace AppBundle\Serializer;

use BadMethodCallException;

use DateTime;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Transaction\Collection,
    AppBundle\Serializer\OperatorSerializer,
    AppBundle\Serializer\BanknoteListSerializer;

class CollectionSerializer extends AbstractSyncSerializer
{
    private $_serializers = [];

    public function setOperatorSerializer(OperatorSerializer $operatorSerializer)
    {
        $this->_serializers[OperatorSerializer::class] = $operatorSerializer;
    }

    public function setBanknoteListSerializer(BanknoteListSerializer $banknoteListSerializer)
    {
        $this->_serializers[BanknoteListSerializer::class] = $banknoteListSerializer;
    }

    static public function getObjectName()
    {
        return 'collection';
    }

    static public function getArrayName()
    {
        return 'collections';
    }

    protected function serialize(PropertiesInterface $collection = NULL)
    {
        return ( $collection instanceof Collection ) ? [
            $collection::PROPERTY_TRANSACTION_AT => $collection->getTransactionAt(),
        ] : FALSE;
    }

    protected function unserialize(array $serializedCollection = NULL)
    {
        $collection = new Collection();

        $collection
            ->setTransactionAt(
                !empty($serializedCollection[$collection::PROPERTY_TRANSACTION_AT])
                    ? new DateTime($serializedCollection[$collection::PROPERTY_TRANSACTION_AT])
                    : NULL
            )
        ;

        return $collection;
    }

    public function syncSerialize(PropertiesInterface $collection = NULL)
    {
        throw new BadMethodCallException('Not implemented!');
    }

    public function syncUnserialize(array $serializedCollection = NULL)
    {
        $collection = $this->unserializeObject($serializedCollection);

        # Operator

        $operatorSerializer = $this->_serializers[OperatorSerializer::class];
        if( !array_key_exists($operatorSerializer::getObjectName(), $serializedCollection) )
            return FALSE;

        $collection->setOperator(
            $operatorSerializer->unserializeObject(
                $serializedCollection[$operatorSerializer::getObjectName()]
            )
        );

        # Banknote Lists

        $banknoteListSerializer = $this->_serializers[BanknoteListSerializer::class];
        if( !array_key_exists($banknoteListSerializer::getArrayName(), $serializedCollection) )
            return FALSE;

        $collection->setBanknoteLists(
            $banknoteListSerializer->unserializeArray(
                $serializedCollection[$banknoteListSerializer::getArrayName()]
            )
        );

        return $collection;
    }
}
