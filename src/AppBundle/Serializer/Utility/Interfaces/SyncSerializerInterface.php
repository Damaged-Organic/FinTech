<?php
// src/AppBundle/Serializer/Utility/Interfaces/SyncSerializerInterface.php
namespace AppBundle\Serializer\Utility\Interfaces;

use Doctrine\ORM\PersistentCollection;

interface SyncSerializerInterface
{
    static public function syncSerializeObject($entity = NULL);

    static public function syncSerializeArray($entities);
}
