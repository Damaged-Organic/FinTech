<?php
// src/AppBundle/Serializer/Utility/Interfaces/SerializerInterface.php
namespace AppBundle\Serializer\Utility\Interfaces;

interface SerializerInterface
{
    static public function serializeObject($entity = NULL);

    static public function serializeArray(array $entities = []);
}
