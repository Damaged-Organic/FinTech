<?php
// AppBundle/Security/Authorization/Voter/EmployeeVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee;

class EmployeeVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const EMPLOYEE_CREATE = 'employee_create';
    const EMPLOYEE_READ   = 'employee_read';
    const EMPLOYEE_UPDATE = 'employee_update';
    const EMPLOYEE_DELETE = 'employee_delete';

    const EMPLOYEE_UPDATE_SYSTEM = 'employee_update_system';

    const EMPLOYEE_READ_ORGANIZATION   = 'employee_read_organization';
    const EMPLOYEE_UPDATE_ORGANIZATION = 'employee_update_organization';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Employee && in_array($attribute, [
            self::EMPLOYEE_CREATE,
            self::EMPLOYEE_READ,
            self::EMPLOYEE_UPDATE,
            self::EMPLOYEE_DELETE,
            self::EMPLOYEE_UPDATE_SYSTEM,
            self::EMPLOYEE_READ_ORGANIZATION,
            self::EMPLOYEE_UPDATE_ORGANIZATION,
        ]);
    }

    protected function voteOnAttribute($attribute, $employee, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::EMPLOYEE_CREATE:
                return $this->create($employee, $user);
            break;

            case self::EMPLOYEE_READ:
                return $this->read($employee, $user);
            break;

            case self::EMPLOYEE_UPDATE:
                return $this->update($employee, $user);
            break;

            case self::EMPLOYEE_DELETE:
                return $this->delete($employee, $user);
            break;

            case self::EMPLOYEE_UPDATE_SYSTEM:
                return $this->updateSystem($employee, $user);
            break;

            case self::EMPLOYEE_READ_ORGANIZATION:
                return $this->readOrganization($employee, $user);
            break;

            case self::EMPLOYEE_UPDATE_ORGANIZATION:
                return $this->updateOrganization($employee, $user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    private function isSuperadmin($user)
    {
        if( $this->hasRole($user, self::ROLE_SUPERADMIN) )
            return TRUE;

        return FALSE;
    }

    private function isAdmin($user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    private function ifUserIsEmployee($user, $employee)
    {
        if( $user->getId() === $employee->getId() )
            return TRUE;

        return FALSE;
    }

    protected function create($employee, $user)
    {
        if( $this->isSuperadmin($employee) )
            return FALSE;

        if( $this->isSuperadmin($user) )
            return TRUE;

        if( $this->isAdmin($employee) )
            return ( $this->isSuperadmin($user) ) ? TRUE : FALSE;

        if( $this->isAdmin($user) )
            return TRUE;

        return FALSE;
    }

    protected function read($employee, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->ifUserIsEmployee($user, $employee) )
            return TRUE;

        return FALSE;
    }

    protected function update($employee, $user)
    {
        if( $this->isSuperadmin($employee) ) {
            return ( $this->ifUserIsEmployee($user, $employee) )
                ? TRUE
                : FALSE;
        }

        if( $this->isSuperadmin($user) )
            return TRUE;

        if( $this->isAdmin($employee) ) {
            return ( $this->ifUserIsEmployee($user, $employee) )
                ? TRUE
                : FALSE;
        }

        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->ifUserIsEmployee($user, $employee) )
            return TRUE;

        return FALSE;
    }

    protected function delete($employee, $user)
    {
        if( $this->isSuperadmin($employee) )
            return FALSE;

        if( $this->isAdmin($employee) )
            return ( $this->isSuperadmin($user) ) ? TRUE : FALSE;

        if( $this->isAdmin($user) )
            return TRUE;

        return FALSE;
    }

    protected function updateSystem($employee, $user)
    {
        if( $this->isSuperadmin($user) )
            return ( !$this->isSuperadmin($employee) ) ? TRUE : FALSE;

        if( $this->isAdmin($user) )
            return ( !$this->isAdmin($employee) ) ? TRUE : FALSE;

        return FALSE;
    }

    protected function readOrganization($employee, $user)
    {
        if( $this->hasRole($employee, self::ROLE_ADMIN) )
            return FALSE;

        return TRUE;
    }

    protected function updateOrganization($employee, $user)
    {
        if(  $this->isSuperadmin($user) )
            return ( !$this->isSuperadmin($employee) ) ? TRUE : FALSE;

        if( $this->isAdmin($user) )
            return ( !$this->isAdmin($employee) ) ? TRUE : FALSE;

        return FALSE;
    }
}
