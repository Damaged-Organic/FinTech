<?php
// src/AppBundle/DataFixtures/ORM/LoadTransaction.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Transaction\Transaction;

class LoadTransaction extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transaction_1 = (new Transaction)
            ->setTransactionId(1)
        ;
        $manager->persist($transaction_1);

        // ---

        $transaction_2 = (new Transaction)
            ->setTransactionId(2)
        ;
        $manager->persist($transaction_2);

        // ---

        $this->addReference('transaction_1', $transaction_1);
        $this->addReference('transaction_2', $transaction_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 9;
    }
}
