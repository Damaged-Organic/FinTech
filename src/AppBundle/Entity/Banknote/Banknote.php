<?php
// src/AppBundle/Entity/Banknote/Banknote.php
namespace AppBundle\Entity\Banknote;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="banknotes")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Banknote\Repository\BanknoteRepository")
 */
class Banknote
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Transaction\Transaction", mappedBy="banknotes")
     */
    protected $transactions;

    protected $currency;

    protected $nominal;

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection;
    }

    /**
     * Add transaction
     *
     * @param \AppBundle\Entity\Banknote\Transaction $transaction
     *
     * @return Banknote
     */
    public function addTransaction(\AppBundle\Entity\Banknote\Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \AppBundle\Entity\Banknote\Transaction $transaction
     */
    public function removeTransaction(\AppBundle\Entity\Banknote\Transaction $transaction)
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
}
