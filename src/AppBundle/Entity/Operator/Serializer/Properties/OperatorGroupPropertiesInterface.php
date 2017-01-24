<?php
// src/AppBundle/Entity/Operator/Serializer/Properties/OperatorGroupPropertiesInterface.php
namespace AppBundle\Entity\Operator\Serializer\Properties;

interface OperatorGroupPropertiesInterface
{
    const OBJECT_NAME_SINGLE = 'operator-group';
    const OBJECT_NAME_BULK   = 'operator-groups';

    const PROPERTY_ID   = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_ROLE = 'role';
}
