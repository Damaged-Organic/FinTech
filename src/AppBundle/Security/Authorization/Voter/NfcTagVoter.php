<?php
// src/AppBundle/Security/Authorization/Voter/NfcTagVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\NfcTag\NfcTag;

class NfcTagVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const NFC_TAG_READ   = 'nfc_tag_read';
    const NFC_TAG_UPDATE = 'nfc_tag_update';
    const NFC_TAG_DELETE = 'nfc_tag_delete';

    const NFC_TAG_BIND   = 'nfc_tag_bind';

    public function supports($attribute, $subject)
    {
        return $subject instanceof NfcTag && in_array($attribute, [
            self::NFC_TAG_READ,
            self::NFC_TAG_UPDATE,
            self::NFC_TAG_DELETE,
            self::NFC_TAG_BIND
        ]);
    }

    protected function voteOnAttribute($attribute, $nfcTag, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::NFC_TAG_READ:
                return $this->read($nfcTag, $user);
            break;

            case self::NFC_TAG_UPDATE:
                return $this->update($nfcTag, $user);
            break;

            case self::NFC_TAG_DELETE:
                return $this->delete($nfcTag, $user);
            break;

            case self::NFC_TAG_BIND:
                return $this->bind($nfcTag, $user);
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

    private function isManagerOfOrganization($nfcTag, $user)
    {
        if( $this->hasRole($user, self::ROLE_MANAGER) ) {
            if( $user instanceof Employee ) {
                if( !$nfcTag->getOperator() )
                    return TRUE;

                return ( $user->getOrganization() === $nfcTag->getOperator()->getOrganization() )
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function read($nfcTag, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($nfcTag, $user) )
            return TRUE;

        return FALSE;
    }

    protected function update($nfcTag, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($nfcTag, $user) )
            return TRUE;

        return FALSE;
    }

    protected function delete($nfcTag, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($nfcTag, $user) )
            return TRUE;

        return FALSE;
    }

    protected function bind($nfcTag, $user)
    {
        if( $this->isAdmin($user) )
            return TRUE;

        if( $this->isManagerOfOrganization($nfcTag, $user) )
            return TRUE;

        return FALSE;
    }
}
