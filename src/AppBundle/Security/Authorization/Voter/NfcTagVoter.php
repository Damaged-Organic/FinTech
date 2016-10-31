<?php
// src/AppBundle/Security/Authorization/Voter/NfcTagVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
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
            case self::OPERATOR_READ:
                return $this->read($user);
            break;

            case self::OPERATOR_UPDATE:
                return $this->update($user);
            break;

            case self::OPERATOR_DELETE:
                return $this->delete($user);
            break;

            case self::OPERATOR_BIND:
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
