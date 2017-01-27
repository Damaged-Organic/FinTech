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
    /**
     * @Method({"GET"})
     * @Route(
     *      "/test",
     *      name="test_dashboard",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function imitationAction($totalSum = NULL)
    {
        $manager = $this->getDoctrine()->getManager();

        $accountGroup = $manager->getRepository('AppBundle:Account\AccountGroup')
            ->find(1);

        if( !($accounts = $accountGroup->getAccounts()) )
            return new Response('No accounts.');

        $formatter = $this->get('sync.banking_server.transfer.formatter');

        $fileRows = [];
        foreach( $accounts as $account )
        {
            if( $totalSum )
                $paymentAmount = bcmul(bcdiv($totalSum, 100), $account->getPercent());
                $account->setPaymentAmount($paymentAmount);

            $transferRecordModel = new TransferRecord(
                $account, $formatter
            );

            $fileRows[] = $transferRecordModel->getTransferRecordRow();
        }

        $accountFile = (new \AppBundle\Entity\Transfer\TransferFile())
            ->setDirname(new \DateTime())
            ->setFilename(13)
        ;

        $transferFileModel = new TransferFile(
            $accountFile, $formatter
        );

        $handler = $this->get('sync.banking_server.sync.handler');

        $handler->connect(
            $this->getParameter('sftp_host'),
            $this->getParameter('sftp_user'),
            $this->getParameter('sftp_pass')
        );

        $result = $handler->syncronize(
            $transferFileModel->getDirname(),
            $transferFileModel->getFilename(),
            implode($fileRows)
        );

        if( $result ) {
            $response = ': )';

            $nominal = ( $totalSum ) ? bcdiv($totalSum, 100) : 0;
            $organization = $manager->getRepository('AppBundle:Organization\Organization')->find(1);
            $bankingMachine = $manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find(1);
            $operator = $manager->getRepository('AppBundle:Operator\Operator')->find(1);

            $banknote = (new \AppBundle\Entity\Banknote\Banknote)
                ->setCurrency(\AppBundle\Entity\Banknote\Banknote::BANKNOTE_CURRENCY_UAH)
                ->setNominal($nominal)
            ;

            $banknoteList = (new \AppBundle\Entity\Banknote\BanknoteList)
                ->setBanknote($banknote)
                ->setQuantity(1)
            ;

            $replenishment = (new \AppBundle\Entity\Transaction\Replenishment)
                ->setSyncId(1)
                ->setSyncAt(new \DateTime())
                ->setTotalAmount()
                ->setOrganization($organization)
                ->setBankingMachine($bankingMachine)
                ->setOperator($operator)
                ->setAccountGroup($accountGroup)
                ->addBanknoteList($banknoteList)
            ;
            $replenishmentFrozen = $replenishment->freeze();

            $manager->persist($banknote);
            $manager->persist($banknoteList);
            $manager->persist($replenishment);
            $manager->persist($replenishmentFrozen);
            $manager->flush();
        } else {
            $response = ': (';
        }

        return new Response($response);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/banking_machines/{serial}/replenishments",
     *      name = "sync_get_banking_machines_replenishments",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function postBankingMachinesReplenishmentsAction(Request $request, $serial)
    {
        $requestContent = $request->getContent();

        if( $requestContent )
        {
            $requestContent = json_decode($request->getContent(), TRUE);

            if( !empty($requestContent['data']) && !empty($requestContent['data']['notes']) )
            {
                $totalSum = 0;

                foreach( $requestContent['data']['notes'] as $note )
                {
                    if( !empty($note['value']) && !empty($note['ammount']) )
                    {
                        $sum = bcmul($note['value'], $note['ammount']);

                        if( $sum )
                            $totalSum = bcadd($totalSum, $sum);
                    } else {
                        return new Response(
                            'Invalid data notes format', 200
                        );
                    }
                }

                if( $totalSum != 0 )
                    $totalSum = bcmul($totalSum, 100);

                $response = $this->forward('SyncBundle:BankingServer:imitation', [
                    'totalSum' => $totalSum,
                ]);

                return new Response(
                    json_encode(['transaction_id' => hash('sha1', uniqid(rand(), TRUE))], JSON_UNESCAPED_UNICODE), 200
                );
            } else {
                return new Response(
                    'Invalid data format', 200
                );
            }
        } else {
            return new Response(
                'No data', 200
            );
        }
    }
}
