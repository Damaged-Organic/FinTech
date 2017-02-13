<?php
// src/AppBundle/Entity/Banknote/BanknoteList.php
namespace AppBundle\Entity\Banknote;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Banknote\Properties\BanknoteListPropertiesInterface;

/**
 * @ORM\Table(name="banknotes_lists")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Banknote\Repository\BanknoteListRepository")
 *
 * @Assert\GroupSequence({"BanknoteList", "Sync"})
 */
class BanknoteList implements BanknoteListPropertiesInterface
{
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
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Transaction\Transaction",
     *     inversedBy="banknoteLists",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $transaction;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Banknote\Banknote",
     *     inversedBy="banknoteLists",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="banknote_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $banknote;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank(groups={"Sync"})
     * @Assert\Type(
     *     type="numeric",
     *     groups={"Sync"}
     * )
     */
    protected $quantity;

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return BanknoteList
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return BanknoteList
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set banknote
     *
     * @param \AppBundle\Entity\Banknote\Banknote $banknote
     *
     * @return Banknote
     */
    public function setBanknote(\AppBundle\Entity\Banknote\Banknote $banknote = null)
    {
        $this->banknote = $banknote;

        return $this;
    }

    /**
     * Get banknote
     *
     * @return \AppBundle\Entity\Banknote\Banknote
     */
    public function getBanknote()
    {
        return $this->banknote;
    }

    /**
     * Set transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return BanknoteList
     */
    public function setTransaction(\AppBundle\Entity\Transaction\Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \AppBundle\Entity\Transaction\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /*-------------------------------------------------------------------------
    | CUSTOM SETTERS\GETTERS
    |------------------------------------------------------------------------*/

    public function getTotalAmount()
    {
        if( !$this->getQuantity() )
            return FALSE;

        if( !$this->getBanknote() || !$this->getBanknote()->getNominal() )
            return FALSE;

        return bcmul(
            $this->getQuantity(), $this->getBanknote()->getNominal(), 2
        );
    }

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_QUANTITY,
        ];
    }
}
