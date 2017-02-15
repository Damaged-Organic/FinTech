<?php
// src/AppBundle/Serializer/Utility/Interfaces/SyncSerializerInterface.php
namespace AppBundle\Serializer\Utility\Interfaces;

use Doctrine\ORM\PersistentCollection;

interface SyncSerializerInterface
{
    public function syncSerializeObject($entity = NULL);

    public function syncSerializeArray($entities);

    public function syncUnserializeObject(array $entity = NULL);

    public function syncUnserializeArray(array $entities);
}
