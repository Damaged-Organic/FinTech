<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSyncSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

abstract class AbstractSyncSerializer implements SyncSerializerInterface
{
    abstract static protected function syncSerialize(PropertiesInterface $entity = NULL);

    static public function syncSerializeObject($entity = NULL)
    {
        return [static::getObjectName() => static::syncSerialize($entity)];
    }

    static public function syncSerializeArray(array $entities)
    {
        $serialized = NULL;

        foreach($entities as $entity) {
            $serialized[] = static::syncSerialize($entity);
        }

        return [static::getArrayName() => $serialized];
    }
}
