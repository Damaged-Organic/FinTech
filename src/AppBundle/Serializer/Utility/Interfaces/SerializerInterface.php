<?php
// src/AppBundle/Serializer/Utility/Interfaces/SerializerInterface.php
namespace AppBundle\Serializer\Utility\Interfaces;

interface SerializerInterface
{
    public function serializeObject($entity = NULL);

    public function serializeArray($entities);

    public function unserializeObject(array $serializedObject = NULL);

    public function unserializeArray(array $serializedObjects);
}
