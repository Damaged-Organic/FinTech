<?php
// src/AppBundle/Security/Authorization/Voter/OperatorVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Operator\Operator;

class OperatorVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const OPERATOR_READ   = 'operator_read';
    const OPERATOR_UPDATE = 'operator_update';
    const OPERATOR_DELETE = 'operator_delete';

    const OPERATOR_BIND = 'operator_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Operator && in_array($attribute, [
            self::OPERATOR_READ,
            self::OPERATOR_UPDATE,
            self::OPERATOR_DELETE,
            self::OPERATOR_BIND,
        ]);
    }

    protected function voteOnAttribute($attribute, $operator, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::OPERATOR_READ:
                return $this->read($operator, $user);
            break;

            case self::OPERATOR_UPDATE:
                return $this->update($operator, $user);
            break;

            case self::OPERATOR_DELETE:
                return $this->delete($operator, $user);
            break;

            case self::OPERATOR_BIND:
                return $this->bind($operator, $user);
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

    private function isManagerOfOrganization($operator, $user)
    {
        if( $this->hasRole($user, self::ROLE_MANAGER) ) {
            if( $user instanceof Employee ) {
                return ( $user->getOrganization() === $operator->getOrganization() )
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function read($operator, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($operator, $user) )
            return TRUE;

        return FALSE;
    }

    protected function update($operator, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($operator, $user) )
            return TRUE;

        return FALSE;
    }

    protected function delete($operator, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($operator, $user) )
            return TRUE;

        return FALSE;
    }

    protected function bind($operator, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($operator, $user) )
            return TRUE;

        return FALSE;
    }
}
