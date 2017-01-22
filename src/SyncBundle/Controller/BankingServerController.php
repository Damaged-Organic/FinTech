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
        $manager = $this->getDoctrine()->getMAnager();

        $transferRecord = $manager->getRepository('SyncBundle:BankingServer\Transfer\TransferRecord')
            ->findAll()[0];

        $formatter = $this->get('sync.banking_server.transfer.formatter');

        $transferRecordModel = new TransferRecord(
            $transferRecord, $formatter
        );

        $transferFileModel = new TransferFile(
            $transferRecord->getTransferFile(), $formatter
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
            $transferRecordModel->getTransferRecordRow()
        );

        if( $result ) {
            $response = ': )';
        } else {
            $response = ': (';
        }

        return new Response($response);
    }
}
