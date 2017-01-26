<?php
// src/AppBundle/Entity/Organization/Organization.php
namespace AppBundle\Entity\Organization;

use DateTime;

use Symfony\Component\HttpFoundation\File\File,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Entity\Organization\Properties\OrganizationPropertiesInterface;

/**
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Organization\Repository\OrganizationRepository")
 *
 * @UniqueEntity(fields="name", message="organization.name.unique")
 *
 * @Assert\GroupSequence({"Organization", "Create"})
 *
 * @Vich\Uploadable
 */
class Organization implements OrganizationPropertiesInterface
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Employee\Employee", mappedBy="organization")
     */
    protected $employees;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Operator\Operator", mappedBy="organization")
     */
    protected $operators;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", mappedBy="organization")
     */
    protected $bankingMachines;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Account\AccountGroup", mappedBy="organization")
     */
    protected $accountGroups;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\Transaction", mappedBy="organization")
     */
    protected $transactions;

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

    /**
     * @Assert\NotBlank(message="organization.logo_file.not_blank", groups={"Create"})
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     *
     * @Vich\UploadableField(mapping="organization_logo", fileNameProperty="logoName")
     */
    protected $logoFile;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $logoName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $logoUpdatedAt;

    public function __construct()
    {
        $this->operators       = new ArrayCollection;
        $this->bankingMachines = new ArrayCollection;
        $this->accountGroups   = new ArrayCollection;
        $this->transactions    = new ArrayCollection;
    }

    public function __toString()
    {
        return ( $this->name ) ? $this->name : "";
    }

    /* Vich Uploadable Methods */

    public function setLogoFile(File $logoFile = NULL)
    {
        $this->logoFile = $logoFile;

        if( $logoFile )
            $this->setLogoUpdatedAt(new DateTime('now'));

        return $this;
    }

    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /* End \ Vich Uploadable Methods */

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
     * Set logoName
     *
     * @param string $logoName
     *
     * @return Organization
     */
    public function setLogoName($logoName)
    {
        $this->logoName = $logoName;

        return $this;
    }

    /**
     * Get logoName
     *
     * @return string
     */
    public function getLogoName()
    {
        return $this->logoName;
    }

    /**
     * Set logoUpdatedAt
     *
     * @param \DateTime $logoUpdatedAt
     *
     * @return Organization
     */
    public function setLogoUpdatedAt($logoUpdatedAt)
    {
        $this->logoUpdatedAt = $logoUpdatedAt;

        return $this;
    }

    /**
     * Get logoUpdatedAt
     *
     * @return \DateTime
     */
    public function getLogoUpdatedAt()
    {
        return $this->logoUpdatedAt;
    }

    /**
     * Add employee
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     *
     * @return Organization
     */
    public function addEmployee(\AppBundle\Entity\Employee\Employee $employee)
    {
        $employee->setOrganization($this);
        $this->employees[] = $employee;

        return $this;
    }

    /**
     * Remove employee
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     */
    public function removeEmployee(\AppBundle\Entity\Employee\Employee $employee)
    {
        $this->employees->removeElement($employee);
    }

    /**
     * Get employees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployees()
    {
        return $this->employees;
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
        $operator->setOrganization($this);
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

    /**
     * Add accountGroup
     *
     * @param \AppBundle\Entity\Account\AccountGroup $accountGroup
     *
     * @return Organization
     */
    public function addAccountGroup(\AppBundle\Entity\Account\AccountGroup $accountGroup)
    {
        $accountGroup->setOrganization($this);
        $this->accountGroups[] = $accountGroup;

        return $this;
    }

    /**
     * Remove accountGroup
     *
     * @param \AppBundle\Entity\Account\AccountGroup $accountGroup
     */
    public function removeAccountGroup(\AppBundle\Entity\Account\AccountGroup $accountGroup)
    {
        $this->accountGroups->removeElement($accountGroup);
    }

    /**
     * Get accountGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccountGroups()
    {
        return $this->accountGroups;
    }

    /**
     * Add transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return Organization
     */
    public function addTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $transaction->setOrganization($this);
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     */
    public function removeTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_ID,
            self::PROPERTY_NAME,
        ];
    }
}
