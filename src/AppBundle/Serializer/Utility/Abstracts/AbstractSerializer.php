<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use AppBundle\Serializer\Utility\Interfaces\SerializerInterface;

abstract class AbstractSerializer implements SerializerInterface
{
    abstract static protected function getObjectName();

    abstract static protected function getArrayName();

    abstract static protected function serialize($entity = NULL);

    static public function serializeObject($entity = NULL)
    {
        return [static::getObjectName() => static::serialize($entity)];
    }

    static public function serializeArray(array $entities = [])
    {
        $serialized = [];

        foreach($entities as $entity) {
            $serialized[] = static::serialize($entity);
        }

        return [static::getArrayName() => $serialized];
    }
}
