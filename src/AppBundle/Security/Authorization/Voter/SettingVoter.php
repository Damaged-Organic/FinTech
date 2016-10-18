<?php
// AppBundle/Security/Authorization/Voter/SettingVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Setting\Setting;

class SettingVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const SETTING_UPDATE = 'setting_update';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Setting && in_array($attribute, [
            self::SETTING_UPDATE
        ]);
    }

    protected function voteOnAttribute($attribute, $setting, TokenInterface $token)
    {
        $user = $token->getUser();
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::SETTING_UPDATE:
                return $this->update($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    public function update($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_SUPERADMIN) )
            return TRUE;

        return FALSE;
    }
}
