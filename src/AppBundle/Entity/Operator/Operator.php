<?php
// src/AppBundle/Entity/Operator/Operator.php
namespace AppBundle\Entity\Operator;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\Operator\Properties\OperatorPropertiesInterface;

/**
 * @ORM\Table(name="operators")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Operator\Repository\OperatorRepository")
 *
 * @Assert\GroupSequence({"Operator", "Sync"})
 */
class Operator implements OperatorPropertiesInterface
{
    use PseudoDeleteMapperTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     *
     * @Assert\NotBlank(groups={"Sync"})
     * @Assert\Type(
     *     type="numeric",
     *     groups={"Sync"}
     * )
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operator\OperatorGroup", inversedBy="operators")
     * @ORM\JoinColumn(name="operator_group_id", referencedColumnName="id")
     */
    protected $operatorGroup;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\NfcTag\NfcTag", mappedBy="operator")
     */
    protected $nfcTag;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization\Organization", inversedBy="operators")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", mappedBy="operators")
     */
    protected $bankingMachines;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Account\AccountGroup", inversedBy="operators")
     * @ORM\JoinTable(name="operators_accounts_groups")
     */
    protected $accountGroups;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\Transaction", mappedBy="operator")
     */
    protected $transactions;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="operator.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="common.human_name.length.min",
     *      maxMessage="common.human_name.length.max"
     * )
     * @CustomAssert\IsHumanName
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="operator.surname.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="common.human_name.length.min",
     *      maxMessage="common.human_name.length.max"
     * )
     * @CustomAssert\IsHumanName
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="operator.patronymic.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="common.human_name.length.min",
     *      maxMessage="common.human_name.length.max"
     * )
     * @CustomAssert\IsHumanName
     */
    protected $patronymic;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumber
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="operator.is_enabled.type",
     *     groups={"Update"}
     * )
     */
    protected $isEnabled = True;

    public function __construct()
    {
        $this->bankingMachines = new ArrayCollection;
        $this->accountGroups   = new ArrayCollection;
        $this->transactions    = new ArrayCollection;
    }

    public function __toString()
    {
        return ( $this->id ) ? $this->id : "";
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Operator
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Operator
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
     * Set surname
     *
     * @param string $surname
     *
     * @return Operator
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set patronymic
     *
     * @param string $patronymic
     *
     * @return Operator
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    /**
     * Get patronymic
     *
     * @return string
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Operator
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     *
     * @return Operator
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set operatorGroup
     *
     * @param \AppBundle\Entity\Operator\OperatorGroup $operatorGroup
     *
     * @return Operator
     */
    public function setOperatorGroup(\AppBundle\Entity\Operator\OperatorGroup $operatorGroup = null)
    {
        $this->operatorGroup = $operatorGroup;

        return $this;
    }

    /**
     * Get operatorGroup
     *
     * @return \AppBundle\Entity\Operator\OperatorGroup
     */
    public function getOperatorGroup()
    {
        return $this->operatorGroup;
    }

    /**
     * Set nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     *
     * @return Operator
     */
    public function setNfcTag(\AppBundle\Entity\NfcTag\NfcTag $nfcTag = null)
    {
        $this->nfcTag = $nfcTag;

        return $this;
    }

    /**
     * Get nfcTag
     *
     * @return \AppBundle\Entity\NfcTag\NfcTag
     */
    public function getNfcTag()
    {
        return $this->nfcTag;
    }

    /**
     * Set organization
     *
     * @param \AppBundle\Entity\Organization\Organization $organization
     *
     * @return Operator
     */
    public function setOrganization(\AppBundle\Entity\Organization\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \AppBundle\Entity\Organization\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Add bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return Operator
     */
    public function addBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine)
    {
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
     * @return Operator
     */
    public function addAccountGroup(\AppBundle\Entity\Account\AccountGroup $accountGroup)
    {
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
     * @return Operator
     */
    public function addTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $transaction->setOperator($this);
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
    | CUSTOM GETTERS
    |------------------------------------------------------------------------*/

    public function getFullName()
    {
        if( !$this->surname || !$this->name )
            return NULL;

        $fullName = [$this->surname, $this->name];

        if( $this->patronymic )
            $fullName[] = $this->patronymic;

        return implode(' ', $fullName);
    }

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_ID,
            self::PROPERTY_FULL_NAME,
        ];
    }
}
