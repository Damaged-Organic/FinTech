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
                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            case self::ORGANIZATION_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            case self::ORGANIZATION_BIND:
                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            default:
                return FALSE;
            break;
        }
    }
}
