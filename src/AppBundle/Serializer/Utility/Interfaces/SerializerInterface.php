<?php
// src/AppBundle/Serializer/Utility/Interfaces/SerializerInterface.php
namespace AppBundle\Serializer\Utility\Interfaces;

use Doctrine\ORM\PersistentCollection;

interface SerializerInterface
{
    static public function serializeObject($entity = NULL);

    static public function serializeArray(PersistentCollection $entities);
}
