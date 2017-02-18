<?php
// src/SyncBundle/Service/BankingMachine/Sync/Manager.php
namespace SyncBundle\Service\BankingMachine\Sync;

use DateTime;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Entity\BankingMachine\BankingMachineSync,
    AppBundle\Entity\BankingMachine\BankingMachineEvent,
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

class Manager
{
    private $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    private function filterManaged(ArrayCollection $managed, callable $filter)
    {
        $filtered = $managed->filter($filter);

        return ( !$filtered->isEmpty() ) ? $filtered->first() : NULL;
    }

    private function getManagedOperators(BankingMachine $bankingMachine)
    {
        return new ArrayCollection(
            $this->_manager->getRepository('AppBundle:Operator\Operator')->findAll()
        );
    }

    private function operatorExistsFilter(Operator $syncOperator)
    {
        return function($operator) use($syncOperator) {
            if( $operator->getId() == $syncOperator->getId() ) return TRUE;
        };
    }

    private function getManagedAccountGroups(BankingMachine $bankingMachine)
    {
        return new ArrayCollection(
            $this->_manager->getRepository('AppBundle:Account\AccountGroup')->findAll()
        );
    }

    private function accountGroupExistsFilter(AccountGroup $syncAccountGroup)
    {
        return function($accountGroup) use($syncAccountGroup) {
            if( $accountGroup->getId() == $syncAccountGroup->getId() ) return TRUE;
        };
    }

    private function getManagedBanknotes(BankingMachine $bankingMachine)
    {
        return new ArrayCollection(
            $this->_manager->getRepository('AppBundle:Banknote\Banknote')->findAll()
        );
    }

    private function banknoteExistsFilter(Banknote $syncBanknote)
    {
        return function($banknote) use($syncBanknote) {
            if( $banknote->getCurrency() == $syncBanknote->getCurrency() &&
                $banknote->getNominal() == $syncBanknote->getNominal() )
                return TRUE;
        };
    }

    public function persistBankingMachineSync(BankingMachine $bankingMachine, BankingMachineSync $bankingMachineSync)
    {
        $bankingMachineSync
            ->setBankingMachine($bankingMachine)
        ;

        $this->_manager->persist($bankingMachineSync);

        return $bankingMachineSync;
    }

    public function persistBankingMachineEvents(BankingMachine $bankingMachine, BankingMachineSync $bankingMachineSync, $bankingMachineEvents)
    {
        foreach($bankingMachineEvents as $bankingMachineEvent)
        {
            $bankingMachineEvent
                ->setBankingMachine($bankingMachine)
                ->setBankingMachineSync($bankingMachineSync)
            ;

            $this->_manager->persist($bankingMachineEvent);
        }

        return $bankingMachineEvents;
    }

    public function persistReplenishments(BankingMachine $bankingMachine, BankingMachineSync $bankingMachineSync, $replenishments)
    {
        $operators     = $this->getManagedOperators($bankingMachine);
        $accountGroups = $this->getManagedAccountGroups($bankingMachine);
        $banknotes     = $this->getManagedBanknotes($bankingMachine);

        foreach($replenishments as $replenishment)
        {
            $replenishment
                ->setBankingMachine($bankingMachine)
                ->setBankingMachineSync($bankingMachineSync)
                ->setOrganization(
                    $bankingMachine->getOrganization()
                )
            ;

            $operatorFilter = $this->operatorExistsFilter($replenishment->getOperator());
            if( !($operator = $this->filterManaged($operators, $operatorFilter)) )
                return FALSE;

            $replenishment->setOperator($operator);

            $accountGroupFilter = $this->accountGroupExistsFilter($replenishment->getAccountGroup());
            if( !($accountGroup = $this->filterManaged($accountGroups, $accountGroupFilter)) )
                return FALSE;

            $replenishment->setAccountGroup($accountGroup);

            foreach( $replenishment->getBanknoteLists() as $banknoteList )
            {
                $banknoteFilter = $this->banknoteExistsFilter($banknoteList->getBanknote());
                if( !($banknote = $this->filterManaged($banknotes, $banknoteFilter)) )
                    return FALSE;

                $banknoteList
                    ->setBanknote($banknote)
                ;
            }

            $replenishmentFrozen = $replenishment->freeze();

            $this->_manager->persist($replenishment);
            $this->_manager->persist($replenishmentFrozen);
        }

        return $replenishments;
    }

    public function persistCollections(BankingMachine $bankingMachine, BankingMachineSync $bankingMachineSync, $collections)
    {
        $operators     = $this->getManagedOperators($bankingMachine);
        $accountGroups = $this->getManagedAccountGroups($bankingMachine);
        $banknotes     = $this->getManagedBanknotes($bankingMachine);

        foreach($collections as $collection)
        {
            $collection
                ->setBankingMachine($bankingMachine)
                ->setBankingMachineSync($bankingMachineSync)
                ->setOrganization(
                    $bankingMachine->getOrganization()
                )
            ;

            $operatorFilter = $this->operatorExistsFilter($collection->getOperator());
            if( !($operator = $this->filterManaged($operators, $operatorFilter)) )
                return FALSE;

            $collection->setOperator($operator);

            foreach( $collection->getBanknoteLists() as $banknoteList )
            {
                $banknoteFilter = $this->banknoteExistsFilter($banknoteList->getBanknote());
                if( !($banknote = $this->filterManaged($banknotes, $banknoteFilter)) )
                    return FALSE;

                $banknoteList
                    ->setBanknote($banknote)
                ;
            }

            $collectionFrozen = $collection->freeze();

            $this->_manager->persist($collection);
            $this->_manager->persist($collectionFrozen);
        }

        return $collections;
    }
}
