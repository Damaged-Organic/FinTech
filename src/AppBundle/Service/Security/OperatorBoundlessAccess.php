<?php
// src/AppBundle/Service/Security/OperatorBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class OperatorBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const OPERATOR_READ   = 'operator_read';
    const OPERATOR_CREATE = 'operator_create';

    const OPERATOR_BIND   = 'operator_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::OPERATOR_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_MANAGER);
            break;

            case self::OPERATOR_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_MANAGER);
            break;

            case self::OPERATOR_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_MANAGER);
            break;

            default:
                return FALSE;
            break;
        }
    }
}
