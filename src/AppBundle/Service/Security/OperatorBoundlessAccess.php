<?php
// src/AppBundle/Service/Security/OperatorBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class OperatorBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const OPERATOR_READ   = 'operator_read';
    const OPERATOR_CREATE = 'operator_create';

    const OPERATOR_BIND = 'operator_bind';

    const OPERATOR_UPDATE_ORGANIZATION = 'operator_update_organization';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::OPERATOR_READ:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::OPERATOR_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::OPERATOR_BIND:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::OPERATOR_UPDATE_ORGANIZATION:
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
