<?php
// src/AppBundle/Entity/Operator/OperatorGroup.php
namespace AppBundle\Entity\Operator;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="operators_groups")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Operator\Repository\OperatorGroupRepository")
 *
 * @UniqueEntity(fields="name", message="operator_group.name.unique")
 */
class OperatorGroup
{
    use IdMapperTrait;

    const ROLE_CASHIER   = 'ROLE_CASHIER';
    const ROLE_COLLECTOR = 'ROLE_COLLECTOR';

    /**
     * @ORM\OneToMany(targetEntity="Operator", mappedBy="operatorGroup")
     */
    private $operators;

    /**
     * @ORM\Column(name="name", type="string", length=20, unique=true)
     *
     * @Assert\NotBlank(message="operator_group.name.not_blank")
     * @Assert\Length(
     *      min=3,
     *      max=20,
     *      minMessage="operator_group.name.length.min",
     *      maxMessage="operator_group.name.length.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(name="role", type="string", length=25, unique=true)
     *
     * @Assert\NotBlank(message="operator_group.role.not_blank")
     * @Assert\Regex(
     *     pattern="#[ROLE_][A-Z]{3,20}#",
     *     message="operator_group.role.regex"
     * )
     */
    private $role;

    public function __construct()
    {
        $this->operators = new ArrayCollection;
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
     * @return OperatorGroup
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
     * Set role
     *
     * @param string $role
     *
     * @return OperatorGroup
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return OperatorGroup
     */
    public function addOperator(\AppBundle\Entity\Operator\Operator $operator)
    {
        $operator->setOperatorGroup($this);
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
}
