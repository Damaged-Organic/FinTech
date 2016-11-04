<?php
// src/AppBundle/Entity/Transaction/Transaction.php
namespace AppBundle\Entity\Transaction;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Transaction\Repository\TransactionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"transaction" = "Transaction", "replenishment" = "Replenishment"})
 */
class Transaction
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Banknote\Banknote", inversedBy="transactions")
     * @ORM\JoinTable(name="transactions_banknotes")
     */
    protected $banknotes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->banknotes = new ArrayCollection;
    }

    public function __toString()
    {
        return (string)$this->transactionId ?: static::class;
    }

    /**
     * Add banknote
     *
     * @param \AppBundle\Entity\Transaction\Banknote $banknote
     *
     * @return Transaction
     */
    public function addBanknote(\AppBundle\Entity\Transaction\Banknote $banknote)
    {
        $this->banknotes[] = $banknote;

        return $this;
    }

    /**
     * Remove banknote
     *
     * @param \AppBundle\Entity\Transaction\Banknote $banknote
     */
    public function removeBanknote(\AppBundle\Entity\Transaction\Banknote $banknote)
    {
        $this->banknotes->removeElement($banknote);
    }

    /**
     * Get banknotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanknotes()
    {
        return $this->banknotes;
    }
}
