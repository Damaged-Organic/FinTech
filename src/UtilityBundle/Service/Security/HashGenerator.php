<?php
// src/UtilityBundle/Service/Security/HashGenerator.php
namespace UtilityBundle\Service\Security;

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
