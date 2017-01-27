<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSyncSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use AppBundle\Serializer\Utility\Abstracts\AbstractSerializer,
    AppBundle\Serializer\Utility\Interfaces\SyncSerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

abstract class AbstractSyncSerializer extends AbstractSerializer implements SyncSerializerInterface
{
    abstract protected function syncSerialize(PropertiesInterface $entity = NULL);

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
}
