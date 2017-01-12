<?php
// src/AppBundle/Service/Security/BankingMachineSyncBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class BankingMachineSyncBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const BANKING_MACHINE_SYNC_READ = 'banking_machine_sync_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::BANKING_MACHINE_SYNC_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}
