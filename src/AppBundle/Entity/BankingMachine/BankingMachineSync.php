<?php
// src/AppBundle/Entity/BankingMachine/BankingMachineSync.php
namespace AppBundle\Entity\BankingMachine;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\BankingMachine\Properties\BankingMachineSyncPropertiesInterface;

/**
 * @ORM\Table(name="banking_machines_sync")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BankingMachine\Repository\BankingMachineSyncRepository")
 *
 * @Assert\GroupSequence({"BankingMachineSync", "Sync"})
 */
class BankingMachineSync implements BankingMachineSyncPropertiesInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", inversedBy="bankingMachineSyncs")
     * @ORM\JoinColumn(name="banking_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $bankingMachine;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\Transaction", mappedBy="bankingMachineSync")
     */
    protected $transactions;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\NotBlank(groups={"Sync"})
     * @Assert\Length(
     *      max=64,
     *      groups={"Sync"}
     * )
     */
    protected $syncId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $syncType;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime(groups={"Sync"})
     */
    protected $syncAt;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $checksum;

    /**
     * @ORM\Column(type="text")
     */
    protected $data;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function __toString()
    {
        return ( $this->syncId ) ? $this->syncId : "";
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return BankingMachineSync
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
     * Set syncId
     *
     * @param string $syncId
     *
     * @return BankingMachineSync
     */
    public function setSyncId($syncId)
    {
        $this->syncId = $syncId;

        return $this;
    }

    /**
     * Get syncId
     *
     * @return string
     */
    public function getSyncId()
    {
        return $this->syncId;
    }

    /**
     * Set syncType
     *
     * @param string $syncType
     *
     * @return BankingMachineSync
     */
    public function setSyncType($syncType)
    {
        $this->syncType = $syncType;

        return $this;
    }

    /**
     * Get syncType
     *
     * @return string
     */
    public function getSyncType()
    {
        return $this->syncType;
    }

    /**
     * Set syncAt
     *
     * @param \DateTime $syncAt
     *
     * @return BankingMachineSync
     */
    public function setSyncAt($syncAt)
    {
        $this->syncAt = $syncAt;

        return $this;
    }

    /**
     * Get syncAt
     *
     * @return \DateTime
     */
    public function getSyncAt()
    {
        return $this->syncAt;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return BankingMachineSync
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return BankingMachineSync
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return BankingMachineSync
     */
    public function setBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine = null)
    {
        $this->bankingMachine = $bankingMachine;

        return $this;
    }

    /**
     * Get bankingMachine
     *
     * @return \AppBundle\Entity\BankingMachine\BankingMachine
     */
    public function getBankingMachine()
    {
        return $this->bankingMachine;
    }

    /**
     * Add transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return BankingMachineSync
     */
    public function addTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $transaction->setBankingMachineSync($this);
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
            self::PROPERTY_SYNC_ID,
            self::PROPERTY_SYNC_TYPE,
            self::PROPERTY_SYNC_AT,
            self::PROPERTY_CHECKSUM,
            self::PROPERTY_DATA,
        ];
    }
}
