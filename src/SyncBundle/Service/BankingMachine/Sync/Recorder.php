<?php
// src/SyncBundle/Service/BankingMachine/Sync/Recorder.php
namespace SyncBundle\Service\BankingMachine\Sync;

use DateTime;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Entity\BankingMachine\BankingMachineSync,
    AppBundle\Serializer\BankingMachineSyncSerializer;

class Recorder
{
    protected $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function recordGetBankingMachinesSync(BankingMachine $bankingMachine, $formattedData)
    {
        $bankingMachineSync = (new BankingMachineSync)
            ->setBankingMachine($bankingMachine)
            ->setSyncId(NULL)
            ->setSyncType(
                'sync_get_banking_machines'
            )
            ->setSyncAt(new DateTime())
            ->setChecksum(
                $formattedData['checksum']
            )
            ->setData(
                json_encode($formattedData['data'])
            )
        ;

        $this->_manager->persist($bankingMachineSync);
        $this->_manager->flush();

        return $bankingMachineSync;
    }
}
