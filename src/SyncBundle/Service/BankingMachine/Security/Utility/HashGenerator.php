<?php
// src/SyncBundle/Service/BankingMachine/Security/Utility/HashGenerator.php
namespace SyncBundle\Service\BankingMachine\Security\Utility;

class HashGenerator
{
    public function getCSPRNG($length)
    {
        return openssl_random_pseudo_bytes($length);
    }

    public function getHashBase64($length)
    {
        return base64_encode($this->getCSPRNG($length));
    }
}
