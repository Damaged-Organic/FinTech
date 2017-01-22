<?php
// src/AppBundle/Entity/Utility/Traits/Security/ApiTokenTrait.php
namespace AppBundle\Entity\Utility\Traits\Security;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

trait ApiTokenTrait
{
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $apiToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $apiTokenExpiresAt;

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return BankingMachine
     */
    private function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    private function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set apiTokenExpiresAt
     *
     * @param \DateTime $apiTokenExpiresAt
     *
     * @return BankingMachine
     */
    private function setApiTokenExpiresAt($apiTokenExpiresAt)
    {
        $this->apiTokenExpiresAt = $apiTokenExpiresAt;

        return $this;
    }

    /**
     * Get apiTokenExpiresAt
     *
     * @return \DateTime
     */
    private function getApiTokenExpiresAt()
    {
        return $this->apiTokenExpiresAt;
    }

    /*-------------------------------------------------------------------------
    | API TOKEN PUBLIC INTERFACE
    |------------------------------------------------------------------------*/

    public function resetApiTokenAndExpirationTime()
    {
        $this
            ->setApiToken(NULL)
            ->setApiTokenExpiresAt(NULL)
        ;
    }

    public function setApiTokenAndExpirationTime($apiToken)
    {
        $apiTokenExpiresAt = (new DateTime("now"))->modify('+1 minute');

        $this
            ->setApiToken($apiToken)
            ->setApiTokenExpiresAt($apiTokenExpiresAt)
        ;
    }

    public function getApiTokenIfNotExpired()
    {
        $apiTokenExpiresAt = $this->getApiTokenExpiresAt();

        if( $apiTokenExpiresAt == NULL )
            return NULL;

        if( $apiTokenExpiresAt < (new DateTime("now")) )
            return NULL;

        return $this->getApiToken();
    }
}
