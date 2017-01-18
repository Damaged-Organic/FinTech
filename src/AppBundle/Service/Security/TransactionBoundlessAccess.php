<?php
// src/AppBundle/Service/Security/TransactionBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class TransactionBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const TRANSACTION_READ = 'transaction_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::TRANSACTION_READ:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            default:
                return FALSE;
            break;
        }
    }
}
