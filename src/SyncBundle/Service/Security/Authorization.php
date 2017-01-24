<?php
// src/SyncBundle/Service/Security/Authorization.php
namespace SyncBundle\Service\Security;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\BankingMachine\BankingMachine;

use UtilityBundle\Service\Security\PasswordEncoder,
    UtilityBundle\Service\Security\HashGenerator;

class Authorization
{
    const TOKEN_LENGTH = 60;
    const TOKEN_HEADER = 'Authorization';

    private $_hashGenerator;
    private $_passwordEncoder;

    public function setPasswordEncoder(PasswordEncoder $passwordEncoder)
    {
        $this->_passwordEncoder = $passwordEncoder;
    }

    public function setHashGenerator(HashGenerator $hashGenerator)
    {
        $this->_hashGenerator = $hashGenerator;
    }

    public function generateToken()
    {
        return $this->_hashGenerator->getHashBase64(self::TOKEN_LENGTH);
    }

    public function encodeToken($token)
    {
        return $this->_passwordEncoder->encodePassword($token);
    }

    public function isAuthorized(Request $request, BankingMachine $bankingMachine)
    {
        if( !$request->headers->has(self::TOKEN_HEADER) )
            return FALSE;

        $token = $request->headers->get(self::TOKEN_HEADER);

        return $this->_passwordEncoder->isPasswordValid(
            $token, $bankingMachine->getApiTokenIfNotExpired()
        );
    }
}
