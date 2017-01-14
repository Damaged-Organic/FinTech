<?php
// src/AppBundle/Service/Security/OrganizationBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class OrganizationBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const ORGANIZATION_READ   = 'organization_read';
    const ORGANIZATION_CREATE = 'organization_create';

    const ORGANIZATION_BIND   = 'organization_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::ORGANIZATION_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_MANAGER);
            break;

            case self::ORGANIZATION_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::ORGANIZATION_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}
