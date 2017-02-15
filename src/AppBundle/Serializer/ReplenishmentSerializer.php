<?php
// src/AppBundle/Serializer/ReplenishmentSerializer.php
namespace AppBundle\Serializer;

use BadMethodCallException;

use AppBundle\Serializer\Utility\Abstracts\AbstractSyncSerializer,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface,
    AppBundle\Entity\Transaction\Replenishment,
    AppBundle\Serializer\OperatorSerializer,
    AppBundle\Serializer\AccountGroupSerializer,
    AppBundle\Serializer\BanknoteListSerializer;

class ReplenishmentSerializer extends AbstractSyncSerializer
{
    private $_serializers = [];

    public function setOperatorSerializer(OperatorSerializer $operatorSerializer)
    {
        $this->_serializers[OperatorSerializer::class] = $operatorSerializer;
    }

    public function setAccountGroupSerializer(AccountGroupSerializer $accountGroupSerializer)
    {
        $this->_serializers[AccountGroupSerializer::class] = $accountGroupSerializer;
    }

    public function setBanknoteListSerializer(BanknoteListSerializer $banknoteListSerializer)
    {
        $this->_serializers[BanknoteListSerializer::class] = $banknoteListSerializer;
    }

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
        ] : FALSE;
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

    public function syncSerialize(PropertiesInterface $replenishment = NULL)
    {
        throw new BadMethodCallException('Not implemented!');
    }

    public function syncUnserialize(array $serializedReplenishment = NULL)
    {
        $replenishment = $this->unserializeObject($serializedReplenishment);

        # Operator

        $operatorSerializer = $this->_serializers[OperatorSerializer::class];
        if( !array_key_exists($operatorSerializer::getObjectName(), $serializedReplenishment) )
            return FALSE;

        $replenishment->setOperator(
            $operatorSerializer->unserializeObject(
                $serializedReplenishment[$operatorSerializer::getObjectName()]
            )
        );

        # Account Group

        $accountGroupSerializer = $this->_serializers[AccountGroupSerializer::class];
        if( !array_key_exists($accountGroupSerializer::getObjectName(), $serializedReplenishment) )
            return FALSE;

        $replenishment->setAccountGroup(
            $accountGroupSerializer->unserializeObject(
                $serializedReplenishment[$accountGroupSerializer::getObjectName()]
            )
        );

        # Banknote Lists

        $banknoteListSerializer = $this->_serializers[BanknoteListSerializer::class];
        if( !array_key_exists($banknoteListSerializer::getArrayName(), $serializedReplenishment) )
            return FALSE;

        $replenishment->setBanknoteLists(
            $banknoteListSerializer->unserializeArray(
                $serializedReplenishment[$banknoteListSerializer::getArrayName()]
            )
        );

        return $replenishment;
    }
}
