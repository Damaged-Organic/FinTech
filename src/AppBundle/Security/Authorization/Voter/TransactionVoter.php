<?php
// src/AppBundle/Security/Authorization/Voter/TransactionVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Transaction\Transaction;

class TransactionVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const TRANSACTION_READ = 'transaction_read';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Transaction && in_array($attribute, [
            self::TRANSACTION_READ,
        ]);
    }

    protected function voteOnAttribute($attribute, $transaction, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::TRANSACTION_READ:
                return $this->read($transaction, $user);
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

    private function isManagerOfOrganization($transaction, $user)
    {
        if( $this->hasRole($user, self::ROLE_MANAGER) ) {
            if( $user instanceof Employee ) {
                return ( $user->getOrganization() === $transaction->getOrganization() )
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function read($transaction, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($transaction, $user) )
            return TRUE;

        return FALSE;
    }
}
