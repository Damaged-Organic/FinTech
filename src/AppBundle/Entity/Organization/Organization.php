<?php
// src/AppBundle/Entity/Organization/Organization.php
namespace AppBundle\Entity\Organization;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait;

/**
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Organization\Repository\OrganizationRepository")
 *
 * @UniqueEntity(fields="name", message="organization.name.unique")
 */
class Organization
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee\Employee", inversedBy="organizations")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $employee;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Operator\Operator", mappedBy="organization")
     */
    protected $operators;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", mappedBy="organization")
     */
    protected $bankingMachines;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="organization.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="organization.name.length.min",
     *      maxMessage="organization.name.length.max"
     * )
     */
    protected $name;

    public function __construct()
    {
        $this->settlements = new ArrayCollection;
    }

    public function __toString()
    {
        return ( $this->name ) ? $this->name : "";
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     *
     * @return Organization
     */
    public function setEmployee(\AppBundle\Entity\Employee\Employee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Add operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return Organization
     */
    public function addOperator(\AppBundle\Entity\Operator\Operator $operator)
    {
        $this->operators[] = $operator;

        return $this;
    }

    /**
     * Remove operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     */
    public function removeOperator(\AppBundle\Entity\Operator\Operator $operator)
    {
        $operator->setOrganization($this);
        $this->operators->removeElement($operator);
    }

    /**
     * Get operators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * Add bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return Organization
     */
    public function addBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine)
    {
        $bankingMachine->setOrganization($this);
        $this->bankingMachines[] = $bankingMachine;

        return $this;
    }

    /**
     * Remove bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     */
    public function removeBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine)
    {
        $this->bankingMachines->removeElement($bankingMachine);
    }

    /**
     * Get bankingMachines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankingMachines()
    {
        return $this->bankingMachines;
    }
}
