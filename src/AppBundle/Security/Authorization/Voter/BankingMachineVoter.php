<?php
// src/AppBundle/Security/Authorization/Voter/BankingMachineVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\BankingMachine\BankingMachine;

class BankingMachineVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const BANKING_MACHINE_READ   = 'banking_machine_read';
    const BANKING_MACHINE_UPDATE = 'banking_machine_update';
    const BANKING_MACHINE_DELETE = 'banking_machine_delete';

    const BANKING_MACHINE_BIND   = 'banking_machine_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof BankingMachine && in_array($attribute, [
            self::BANKING_MACHINE_READ,
            self::BANKING_MACHINE_UPDATE,
            self::BANKING_MACHINE_DELETE,
            self::BANKING_MACHINE_BIND
        ]);
    }

    protected function voteOnAttribute($attribute, $bankingMachine, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::BANKING_MACHINE_READ:
                return $this->read($user);
            break;

            case self::BANKING_MACHINE_UPDATE:
                return $this->update($user);
            break;

            case self::BANKING_MACHINE_DELETE:
                return $this->delete($user);
            break;

            case self::BANKING_MACHINE_BIND:
                return $this->bind($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function read($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_EMPLOYEE) )
            return TRUE;

        return FALSE;
    }

    protected function update($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function delete($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function bind($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }
}
