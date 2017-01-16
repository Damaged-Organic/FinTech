<?php
// src/AppBundle/Security/Authorization/Voter/AccountGroupVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Account\AccountGroup;

class AccountGroupVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const ACCOUNT_GROUP_READ   = 'account_group_read';
    const ACCOUNT_GROUP_UPDATE = 'account_group_update';
    const ACCOUNT_GROUP_DELETE = 'account_group_delete';

    const ACCOUNT_GROUP_BIND   = 'account_group_bind';

    const ACCOUNT_GROUP_UPDATE_ORGANIZATION = 'account_group_update_organization';

    public function supports($attribute, $subject)
    {
        return $subject instanceof AccountGroup && in_array($attribute, [
            self::ACCOUNT_GROUP_READ,
            self::ACCOUNT_GROUP_UPDATE,
            self::ACCOUNT_GROUP_DELETE,
            self::ACCOUNT_GROUP_BIND,
            self::ACCOUNT_GROUP_UPDATE_ORGANIZATION
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
                return $this->read($accountGroup, $user);
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

            case self::ACCOUNT_GROUP_UPDATE_ORGANIZATION:
                return $this->updateOrganization($user);
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

    private function isManagerOfOrganization($accountGroup, $user)
    {
        if( $this->hasRole($user, self::ROLE_MANAGER) ) {
            if( $user instanceof Employee ) {
                return ( $user->getOrganization() === $accountGroup->getOrganization() )
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function read($accountGroup, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($accountGroup, $user) )
            return TRUE;

        return FALSE;
    }

    protected function update($user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        return TRUE;
    }

    protected function delete($user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        return FALSE;
    }

    protected function bind($user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        return FALSE;
    }

    protected function updateOrganization($user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        return FALSE;
    }
}
