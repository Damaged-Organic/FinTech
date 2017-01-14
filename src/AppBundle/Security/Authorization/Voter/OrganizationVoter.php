<?php
// src/AppBundle/Security/Authorization/Voter/OrganizationVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Organization\Organization;

class OrganizationVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const ORGANIZATION_READ   = 'organization_read';
    const ORGANIZATION_UPDATE = 'organization_update';
    const ORGANIZATION_DELETE = 'organization_delete';

    const ORGANIZATION_BIND   = 'organization_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Organization && in_array($attribute, [
            self::ORGANIZATION_READ,
            self::ORGANIZATION_UPDATE,
            self::ORGANIZATION_DELETE,
            self::ORGANIZATION_BIND
        ]);
    }

    protected function voteOnAttribute($attribute, $organization, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::ORGANIZATION_READ:
                return $this->read($organization, $user);
            break;

            case self::ORGANIZATION_UPDATE:
                return $this->update($user);
            break;

            case self::ORGANIZATION_DELETE:
                return $this->delete($user);
            break;

            case self::ORGANIZATION_BIND:
                return $this->bind($user);
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

    private function isManagerOfOrganization($organization, $user)
    {
        if( $this->hasRole($user, self::ROLE_MANAGER) ) {
            if( $user instanceof Employee ) {
                return ( $user->getOrganization() === $organization )
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function read($organization, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($organization, $user) )
            return TRUE;

        return FALSE;
    }

    protected function update($user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        return FALSE;
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
}
