<?php
// src/AppBundle/Security/Authorization/Voter/AccountVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Account\Account;

class AccountVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const ACCOUNT_READ   = 'account_read';
    const ACCOUNT_UPDATE = 'account_update';
    const ACCOUNT_DELETE = 'account_delete';

    const ACCOUNT_BIND = 'account_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Account && in_array($attribute, [
            self::ACCOUNT_READ,
            self::ACCOUNT_UPDATE,
            self::ACCOUNT_DELETE,
            self::ACCOUNT_BIND,
        ]);
    }

    protected function voteOnAttribute($attribute, $account, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::ACCOUNT_READ:
                return $this->read($account, $user);
            break;

            case self::ACCOUNT_UPDATE:
                return $this->update($account, $user);
            break;

            case self::ACCOUNT_DELETE:
                return $this->delete($account, $user);
            break;

            case self::ACCOUNT_BIND:
                return $this->bind($account, $user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    private function isAdmin($user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    private function isManagerOfOrganization($account, $user)
    {
        if( $this->hasRole($user, self::ROLE_MANAGER) ) {
            if( $user instanceof Employee ) {
                if( !$account->getAccountGroup() )
                    return TRUE;

                return ( $user->getOrganization() === $account->getAccountGroup()->getOrganization() )
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function read($account, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($account, $user) )
            return TRUE;

        return FALSE;
    }

    protected function update($account, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($account, $user) )
            return TRUE;

        return FALSE;
    }

    protected function delete($account, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($account, $user) )
            return TRUE;

        return FALSE;
    }

    protected function bind($account, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($account, $user) )
            return TRUE;

        return FALSE;
    }
}
