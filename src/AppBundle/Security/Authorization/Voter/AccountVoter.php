<?php
// src/AppBundle/Security/Authorization/Voter/AccountVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Account\Account;

class AccountVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const ACCOUNT_READ   = 'account_read';
    const ACCOUNT_UPDATE = 'account_update';
    const ACCOUNT_DELETE = 'account_delete';

    const ACCOUNT_BIND   = 'account_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Account && in_array($attribute, [
            self::ACCOUNT_READ,
            self::ACCOUNT_UPDATE,
            self::ACCOUNT_DELETE,
            self::ACCOUNT_BIND
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
                return $this->read($user);
            break;

            case self::ACCOUNT_UPDATE:
                return $this->update($user);
            break;

            case self::ACCOUNT_DELETE:
                return $this->delete($user);
            break;

            case self::ACCOUNT_BIND:
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
