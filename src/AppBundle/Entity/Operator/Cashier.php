<?php
// src/AppBundle/Entity/Operator/Cashier.php
namespace AppBundle\Entity\Operator;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Entity\Operator\Operator;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Operator\Repository\OperatorRepository")
 */
class Cashier extends Operator
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $personalId;

    /**
     * Set personalId
     *
     * @param string $personalId
     *
     * @return Cashier
     */
    public function setPersonalId($personalId)
    {
        $this->personalId = $personalId;

        return $this;
    }

    /**
     * Get personalId
     *
     * @return string
     */
    public function getPersonalId()
    {
        return $this->personalId;
    }
}
