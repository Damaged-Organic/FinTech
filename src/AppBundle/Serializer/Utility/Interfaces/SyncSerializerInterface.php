<?php
// src/AppBundle/Serializer/Utility/Interfaces/SyncSerializerInterface.php
namespace AppBundle\Serializer\Utility\Interfaces;

interface SyncSerializerInterface
{
    static public function serializeForSync($entity);
}
