<?php
// src/AppBundle/Security/Authorization/Voter/AccountGroupVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Account\AccountGroup;

class AccountGroupVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const ACCOUNT_GROUP_READ   = 'account_group_read';
    const ACCOUNT_GROUP_UPDATE = 'account_group_update';
    const ACCOUNT_GROUP_DELETE = 'account_group_delete';

    const ACCOUNT_GROUP_BIND   = 'account_group_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof AccountGroup && in_array($attribute, [
            self::ACCOUNT_GROUP_READ,
            self::ACCOUNT_GROUP_UPDATE,
            self::ACCOUNT_GROUP_DELETE,
            self::ACCOUNT_GROUP_BIND
        ]);
    }

    protected function voteOnAttribute($attribute, $accountGroup, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::ACCOUNT_GROUP_READ:
                return $this->read($user);
            break;

            case self::ACCOUNT_GROUP_UPDATE:
                return $this->update($user);
            break;

            case self::ACCOUNT_GROUP_DELETE:
                return $this->delete($user);
            break;

            case self::ACCOUNT_GROUP_BIND:
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
