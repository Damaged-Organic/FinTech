<?php
// src/AppBundle/Serializer/Utility/Abstracts/AbstractSerializer.php
namespace AppBundle\Serializer\Utility\Abstracts;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use AppBundle\Serializer\Utility\Interfaces\SerializerInterface,
    AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

abstract class AbstractSerializer implements SerializerInterface
{
    protected $_validator;

    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
    }

    static protected function getObjectName()
    {
        throw new Exception('Method not implemented');
    }

    static protected function getArrayName()
    {
        throw new Exception('Method not implemented');
    }

    abstract protected function serialize(PropertiesInterface $entity = NULL);

    abstract protected function unserialize(array $serializedObject = NULL);

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

    public function unserializeObject(array $serializedObject = NULL)
    {
        $object = $this->unserialize($serializedObject);

        $errors = $this->_validator->validate($object, NULL, ['Sync']);

        // Could return detailed errors list in a pinch
        return ( count($errors) > 0 ) ? FALSE : $object;
    }

    public function unserializeArray(array $serializedObjects)
    {
        $unserialized = NULL;

        foreach( $serializedObjects as $serializedObject ) {
            if( $entity = $this->unserialize($serializedObject) ) {
                $unserialized[] = $entity;
            } else {
                return FALSE;
            }
        }

        return $unserialized;
    }
}
