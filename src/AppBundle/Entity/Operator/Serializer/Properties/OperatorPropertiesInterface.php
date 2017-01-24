<?php
// src/AppBundle/Entity/Operator/Serializer/Properties/OperatorPropertiesInterface.php
namespace AppBundle\Entity\Operator\Serializer\Properties;

interface OperatorPropertiesInterface
{
    const OBJECT_NAME_SINGLE = 'operator';
    const OBJECT_NAME_BULK   = 'operators';

    const PROPERTY_ID        = 'id';
    const PROPERTY_FULL_NAME = 'full-name';
}
