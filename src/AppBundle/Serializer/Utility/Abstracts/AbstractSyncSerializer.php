<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSyncSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use RuntimeException;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

abstract class AbstractSyncSerializer extends AbstractSerializer implements SyncSerializerInterface
{
    abstract protected function syncSerialize(PropertiesInterface $entity = NULL);

    abstract protected function syncUnserialize(array $serializedEntity = NULL);

    public function syncSerializeObject($entity = NULL)
    {
        return [static::getObjectName() => $this->syncSerialize($entity)];
    }

    public function syncSerializeArray($entities)
    {
        $serialized = NULL;

        foreach($entities as $entity) {
            if( $entity = $this->syncSerialize($entity) )
                $serialized[] = $entity;
        }

        return [static::getArrayName() => $serialized];
    }

    public function syncUnserializeObject(array $serializedObject = NULL)
    {
        return $this->syncUnserialize($serializedObject);
    }

    public function syncUnserializeArray(array $serializedObjects)
    {
        $unserialized = NULL;

        foreach( $serializedObjects as $serializedObject ) {
            if( $entity = $this->syncUnserialize($serializedObject) ) {
                $unserialized[] = $entity;
            } else {
                throw new RuntimeException(
                    get_called_class() . " is unable to unserialize object with invalid structure"
                );
            }
        }

        return $unserialized;
    }
}
