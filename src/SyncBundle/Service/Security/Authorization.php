<?php
// src/SyncBundle/Service/BankingMachine/Security/Authorization.php
namespace SyncBundle\Service\BankingMachine\Security;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\BankingMachine\BankingMachine;

use SyncBundle\Service\BankingMachine\Security\Utility\PasswordEncoder,
    SyncBundle\Service\BankingMachine\Security\Utility\HashGenerator;

class Authorization
{
    const TOKEN_LENGTH = 60;

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
        $token = NULL;

        return $this->_passwordEncoder->isPasswordValid(
            $token, $bankingMachine->getApiTokenIfNotExpired()
        );
    }
}
