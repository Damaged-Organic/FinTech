<?php
// src/AppBundle/Service/Security/NfcTagBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class NfcTagBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const NFC_TAG_READ   = 'nfc_tag_read';
    const NFC_TAG_CREATE = 'nfc_tag_create';

    const NFC_TAG_BIND   = 'nfc_tag_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::NFC_TAG_READ:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::NFC_TAG_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::NFC_TAG_BIND:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            default:
                return FALSE;
            break;
        }
    }
}
