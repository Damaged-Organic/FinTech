<?php
// src/SyncBundle/Controller/BankingServerController.php
namespace SyncBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use JMS\DiExtraBundle\Annotation as DI;

use SyncBundle\Model\BankingServer\Transfer\TransferRecord,
    SyncBundle\Model\BankingServer\Transfer\TransferFile;

class BankingServerController extends Controller
{
    public function transferAction($syncId)
    {
        $manager = $this->getDoctrine()->getManager();

        $transactions = $manager->getRepository('AppBundle:Transaction\Transaction')
            ->findBy(['syncId' => $syncId]);

        $handler = $this->get('sync.banking_server.sync.handler');

        $handler->connect(
            $this->getParameter('sftp_host'),
            $this->getParameter('sftp_user'),
            $this->getParameter('sftp_pass')
        );

        foreach( $transactions as $transaction )
        {
            $accountGroup = $transaction->getAccountGroup();

            if( !($accounts = $accountGroup->getAccounts()) )
                return new Response('No accounts.');

            $formatter = $this->get('sync.banking_server.transfer.formatter');

            $fileRows = [];
            foreach( $accounts as $account )
            {
                $paymentAmount = bcmul(
                    bcdiv($transaction->getTransactionFunds(), 100),
                    $account->getPercent()
                );
                $account->setPaymentAmount($paymentAmount);

                $transferRecordModel = new TransferRecord(
                    $account, $formatter
                );

                $fileRows[] = $transferRecordModel->getTransferRecordRow();
            }

            $accountFile = (new \AppBundle\Entity\Transfer\TransferFile())
                ->setDirname(NULL)
                ->setFilename($transaction->getId())
            ;

            $transferFileModel = new TransferFile(
                $accountFile, $formatter
            );

            $result = $handler->syncronize(
                $transferFileModel->getDirname(),
                $transferFileModel->getFilename(),
                implode($fileRows)
            );
        }

        return new Response('OK');
    }
}
