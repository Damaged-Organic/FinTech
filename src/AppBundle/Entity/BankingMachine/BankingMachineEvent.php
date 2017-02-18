<?php
// src/AppBundle/Entity/BankingMachine/BankingMachineEvent.php
namespace AppBundle\Entity\BankingMachine;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\BankingMachine\Properties\BankingMachineEventPropertiesInterface;

/**
 * @ORM\Table(name="banking_machines_events")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BankingMachine\Repository\BankingMachineEventRepository")
 *
 * @Assert\GroupSequence({"BankingMachineEvent", "Sync"})
 */
class BankingMachineEvent implements BankingMachineEventPropertiesInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", inversedBy="bankingMachineEvents")
     * @ORM\JoinColumn(name="banking_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $bankingMachine;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BankingMachine\BankingMachineSync", inversedBy="bankingMachineEvents")
     * @ORM\JoinColumn(name="banking_machine_sync_id", referencedColumnName="id")
     */
    protected $bankingMachineSync;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $eventId;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime(groups={"Sync"})
     */
    protected $eventAt;

    /**
     * @ORM\Column(type="string", length=32)
     *
     * @Assert\NotBlank(groups={"Sync"})
     * @Assert\Length(
     *      max=32,
     *      groups={"Sync"}
     * )
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=16)
     *
     * @Assert\Length(
     *      max=16,
     *      groups={"Sync"}
     * )
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     * @Assert\Length(
     *      max=250,
     *      groups={"Sync"}
     * )
     */
    protected $message;

    public function __toString()
    {
        return (string)$this->eventId ?: static::class;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return BankingMachineEvent
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
     * Set eventId
     *
     * @param string $eventId
     *
     * @return BankingMachineEvent
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set eventAt
     *
     * @param \DateTime $eventAt
     *
     * @return BankingMachineEvent
     */
    public function setEventAt($eventAt)
    {
        $this->eventAt = $eventAt;

        return $this;
    }

    /**
     * Get eventAt
     *
     * @return \DateTime
     */
    public function getEventAt()
    {
        return $this->eventAt;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return BankingMachineEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code
     *
     * @param integer $code
     *
     * @return BankingMachineEvent
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return BankingMachineEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return BankingMachineEvent
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
     * Set bankingMachineSync
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachineSync $bankingMachineSync
     *
     * @return BankingMachineEvent
     */
    public function setBankingMachineSync(\AppBundle\Entity\BankingMachine\BankingMachineSync $bankingMachineSync = null)
    {
        $this->bankingMachineSync = $bankingMachineSync;

        return $this;
    }

    /**
     * Get bankingMachineSync
     *
     * @return \AppBundle\Entity\BankingMachine\BankingMachineSync
     */
    public function getBankingMachineSync()
    {
        return $this->bankingMachineSync;
    }

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_EVENT_AT,
            self::PROPERTY_TYPE,
            self::PROPERTY_CODE,
            self::PROPERTY_MESSAGE,
        ];
    }
}
