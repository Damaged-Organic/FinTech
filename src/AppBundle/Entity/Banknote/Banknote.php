<?php
// src/AppBundle/Entity/Banknote/Banknote.php
namespace AppBundle\Entity\Banknote;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\Banknote\Properties\BanknotePropertiesInterface,
    AppBundle\Entity\Banknote\Utility\Interfaces\BanknoteCurrencyListInterface;

/**
 * @ORM\Table(name="banknotes")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Banknote\Repository\BanknoteRepository")
 *
 * @Assert\GroupSequence({"Banknote", "Sync"})
 */
class Banknote implements BanknotePropertiesInterface, BanknoteCurrencyListInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Banknote\BanknoteList", mappedBy="banknote")
     */
    protected $banknoteLists;

    /**
     * @ORM\Column(type="string", length=3)
     *
     * @Assert\NotBlank(groups={"Sync"})
     * @Assert\Length(
     *      min=3,
     *      max=3,
     *      groups={"Sync"}
     * )
     */
    protected $currency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     *
     * @CustomAssert\IsDecimal(groups={"Sync"})
     */
    protected $nominal;

    public function __construct()
    {
        $this->banknoteLists = new ArrayCollection;
    }

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Banknote
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
     * Set currency
     *
     * @param string $currency
     *
     * @return Banknote
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set nominal
     *
     * @param string $nominal
     *
     * @return Banknote
     */
    public function setNominal($nominal)
    {
        $this->nominal = $nominal;

        return $this;
    }

    /**
     * Get nominal
     *
     * @return string
     */
    public function getNominal()
    {
        return $this->nominal;
    }

    /**
     * Add banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     *
     * @return Banknote
     */
    public function addBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
        $this->banknoteLists[] = $banknoteList;

        return $this;
    }

    /**
     * Remove banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     */
    public function removeBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
        $this->banknoteLists->removeElement($banknoteList);
    }

    /**
     * Get banknoteLists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanknoteLists()
    {
        return $this->banknoteLists;
    }

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_CURRENCY,
            self::PROPERTY_NOMINAL,
        ];
    }

    static public function getBanknoteCurrencyList()
    {
        return [self::BANKNOTE_CURRENCY_UAH];
    }
}
