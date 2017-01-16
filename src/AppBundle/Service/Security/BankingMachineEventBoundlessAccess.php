<?php
// src/AppBundle/Service/Security/BankingMachineEventBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class BankingMachineEventBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const BANKING_MACHINE_EVENT_READ = 'banking_machine_event_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::BANKING_MACHINE_EVENT_READ:
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
