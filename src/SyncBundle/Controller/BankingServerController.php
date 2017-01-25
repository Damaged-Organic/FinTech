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
    public function testAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $accountGroup = $manager->getRepository('AppBundle:Account\AccountGroup')
            ->findAll()[0];

        if( !($accounts = $accountGroup->getAccounts()) )
            return new Response('No accounts.');

        $formatter = $this->get('sync.banking_server.transfer.formatter');

        $fileRows = [];
        foreach( $accounts as $account )
        {
            $transferRecordModel = new TransferRecord(
                $account, $formatter
            );

            $fileRows[] = $transferRecordModel->getTransferRecordRow();
        }

        $accountFile = (new \AppBundle\Entity\Transfer\TransferFile())
            ->setDirname(new \DateTime())
            ->setFilename(1)
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
        } else {
            $response = ': (';
        }

        return new Response($response);
    }
}
