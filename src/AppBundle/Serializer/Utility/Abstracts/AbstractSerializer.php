<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use Doctrine\ORM\PersistentCollection;

use AppBundle\Serializer\Utility\Interfaces\SerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

abstract class AbstractSerializer implements SerializerInterface
{
    abstract static protected function getObjectName();

    abstract static protected function getArrayName();

    abstract static protected function serialize(PropertiesInterface $entity = NULL);

    static public function serializeObject($entity = NULL)
    {
        return [static::getObjectName() => static::serialize($entity)];
    }

    static public function serializeArray(PersistentCollection $entities)
    {
        $serialized = NULL;

        foreach($entities as $entity) {
            if( $entity = static::serialize($entity) )
                $serialized[] = $entity;
        }

        return [static::getArrayName() => $serialized];
    }
}
