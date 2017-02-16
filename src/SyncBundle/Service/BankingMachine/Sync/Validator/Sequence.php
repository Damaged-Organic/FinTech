<?php
// src/SyncBundle/Service/BankingMachine/Sync/Validator/Sequence.php
namespace SyncBundle\Service\BankingMachine\Sync\Validator;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Entity\BankingMachine\BankingMachineSync;

class Sequence
{
    private $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function isAlreadyPersisted(BankingMachine $bankingMachine, BankingMachineSync $bankingMachineSync)
    {
        $bankingMachineSync = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachineSync')->findOneBy([
            'bankingMachine' => $bankingMachine,
            'syncId'         => $bankingMachineSync->getSyncId(),
            'syncType'       => $bankingMachineSync->getSyncType(),
        ]);

        return $bankingMachineSync;
    }
}
