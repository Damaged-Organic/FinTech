<?php
// src/AppBundle/Entity/Operator/Collector.php
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
class Collector extends Operator
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $dutyId;

    /**
     * Set dutyId
     *
     * @param string $dutyId
     *
     * @return Collector
     */
    public function setDutyId($dutyId)
    {
        $this->dutyId = $dutyId;

        return $this;
    }

    /**
     * Get dutyId
     *
     * @return string
     */
    public function getDutyId()
    {
        return $this->dutyId;
    }
}
