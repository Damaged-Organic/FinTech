<?php
// src/SyncBundle/Service/BankingMachine/Security/PasswordEncoder.php
namespace SyncBundle\Service\BankingMachine\Security;

class PasswordEncoder
{
    public function encodePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12
        ]);
    }

    public function isPasswordValid($password, $encoded)
    {
        return password_verify($password, $encoded);
    }
}