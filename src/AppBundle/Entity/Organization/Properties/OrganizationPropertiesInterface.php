<?php
// src/AppBundle/Entity/Organization/Properties/OrganizationPropertiesInterface.php
namespace AppBundle\Entity\Organization\Properties;

use AppBundle\Entity\Utility\Interfaces\PropertiesInterface;

interface OrganizationPropertiesInterface extends PropertiesInterface
{
    const PROPERTY_ID        = 'id';
    const PROPERTY_NAME      = 'name';
    const PROPERTY_LOGO_FILE = 'logo-file';
}
