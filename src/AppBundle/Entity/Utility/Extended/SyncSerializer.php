<?php
// src/AppBundle/Entity/Utility/Extended/SyncSerializer.php
namespace AppBundle\Entity\Utility\Extended;

abstract class SyncSerializer
{
    abstract static protected function serialize();

    static public function serializeSingle($entity = NULL)
    {
        return [
            $entity::OBJECT_NAME_SINGLE => static::serialize($entity)
        ];
    }

    static public function serializeBulk($entities = NULL)
    {
        $serialized = [];

        foreach((array)$entities as $entity) {
            $serialized[] = static::serialize($entity);
        }

        return [
            $entity::OBJECT_NAME_BULK => $serialized
        ];
    }
}
