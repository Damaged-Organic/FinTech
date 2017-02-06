<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use AppBundle\Serializer\Utility\Interfaces\SerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

abstract class AbstractSerializer implements SerializerInterface
{
    static protected function getObjectName()
    {
        throw new Exception('Method not implemented');
    }

    static protected function getArrayName()
    {
        throw new Exception('Method not implemented');
    }

    abstract protected function serialize(PropertiesInterface $entity = NULL);

    public function serializeObject($entity = NULL)
    {
        return [static::getObjectName() => $this->serialize($entity)];
    }

    public function serializeArray($entities)
    {
        $serialized = NULL;

        foreach($entities as $entity) {
            if( $entity = $this->serialize($entity) )
                $serialized[] = $entity;
        }

        return [static::getArrayName() => $serialized];
    }
}
