<?php
// src/SyncBundle/Service/BankingMachine/Sync/Handler.php
namespace SyncBundle\Service\BankingMachine\Sync;

use DateTime;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Entity\Transaction\Replenishment,
    AppBundle\Serializer\ReplenishmentSerializer,
    AppBundle\Entity\Operator\Operator,
    AppBundle\Serializer\OperatorSerializer,
    AppBundle\Entity\Account\AccountGroup,
    AppBundle\Serializer\AccountGroupSerializer,
    AppBundle\Entity\Banknote\Banknote,
    AppBundle\Serializer\BanknoteSerializer,
    AppBundle\Entity\Banknote\BanknoteList,
    AppBundle\Serializer\BanknoteListSerializer;

use SyncBundle\Service\BankingMachine\Sync\Interfaces\SyncDataInterface;

class Handler implements SyncDataInterface
{
    private $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function handleReplenishmentData(BankingMachine $bankingMachine, $data)
    {
        // Operators in collection indexed by `id`!
        if( !($operators = $bankingMachine->getOperators()) ) {
            throw new Exception('No operators bound to banking machine');
        }

        // Account groups in collection indexed by `id`!
        if( !($accountGroups = $bankingMachine->getAccountGroups()) ) {
            throw new Exception('No account groups bound to banking machine');
        }

        $replenishmentsArray = [];
        $banknoteListsArray  = [];

        foreach( $data[self::SYNC_DATA][ReplenishmentSerializer::getArrayName()] as $replenishmentArray )
        {
            $operatorObjectName = OperatorSerializer::getObjectName();
            $operatorExists = (
                !empty($replenishmentArray[$operatorObjectName][Operator::PROPERTY_ID]) &&
                $operators->get($replenishmentArray[$operatorObjectName][Operator::PROPERTY_ID])
            );

            $accountGroupObjectName = AccountGroupSerializer::getObjectName();
            $accountGroupExists = (
                !empty($replenishmentArray[$accountGroupObjectName][AccountGroup::PROPERTY_ID]) &&
                $accountGroups->get($replenishmentArray[$accountGroupObjectName][AccountGroup::PROPERTY_ID])
            );

            if( $operatorExists && $accountGroupExists )
            {
                $replenishment = (new Replenishment())
                    ->setSyncId($data[self::SYNC_DATA]['sync']['id'])
                    ->setSyncAt(new DateTime($data[self::SYNC_DATA]['sync']['at']))
                    ->setTransactionAt(new DateTime())
                    ->setBankingMachine($bankingMachine)
                    ->setOrganization($bankingMachine->getOrganization())
                    ->setOperator(
                        $operators->get($replenishmentArray[$operatorObjectName][Operator::PROPERTY_ID])
                    )
                    ->setAccountGroup(
                        $accountGroups->get($replenishmentArray[$accountGroupObjectName][AccountGroup::PROPERTY_ID])
                    )
                ;

                // $replenishment
                //     ->setId(
                //         $this->_manager->getRepository('AppBundle:Transaction\Transaction')->rawInsertReplenishment($replenishment)
                //     )
                // ;

                $banknoteArrayName = BanknoteSerializer::getArrayName();
                foreach( $replenishmentArray[$banknoteArrayName] as $banknoteArray )
                {
                    $banknotes = new ArrayCollection($this->_manager->getRepository('AppBundle:Banknote\Banknote')->findAll());

                    $matchingBanknoteCollection = $banknotes->filter(function($banknote) use($banknoteArray) {
                        if( $banknote->getCurrency() == $banknoteArray[Banknote::PROPERTY_CURRENCY] &&
                            $banknote->getNominal() == $banknoteArray[Banknote::PROPERTY_NOMINAL] ) {
                            return TRUE;
                        }
                    });
                    $banknote = ( !$matchingBanknoteCollection->isEmpty() ) ? $matchingBanknoteCollection->first() : NULL;

                    if( $banknote )
                    {
                        $banknoteList = (new BanknoteList)
                            ->setTransaction($replenishment)
                            ->setBanknote($banknote)
                            ->setQuantity($banknoteArray[BanknoteList::PROPERTY_QUANTITY])
                        ;

                        $replenishment->addBanknoteList($banknoteList);

                        $this->_manager->persist($banknoteList);

                        $banknoteListsArray[] = $banknoteList;
                    } else {
                        // Logging value that somehow (!) contains wrong bindings
                    }
                }

                $replenishmentFrozen = $replenishment->freeze();
                $this->_manager->persist($replenishment);
                $this->_manager->persist($replenishmentFrozen);

                $replenishmentsArray[] = $replenishment;
            } else {
                // Logging value that somehow (!) contains wrong bindings
            }
        }

        // if( $replenishmentsArray )
        // {
        //     $this->_manager->getRepository('AppBundle:Transaction\Transaction')->rawUpdateTransactionsFunds($replenishmentsArray);
        //
        //     // When banknote lists empty?
        //     if( $banknoteListsArray )
        //     {
        //         $this->_manager->getRepository('AppBundle:Banknote\BanknoteList')->rawInsertBanknoteLists($banknoteListsArray);
        //     }
        // }

        return $data[self::SYNC_DATA]['sync']['id'];
    }
}
