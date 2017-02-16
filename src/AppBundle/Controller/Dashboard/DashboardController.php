<?php
// AppBundle/Controller/Dashboard/DashboardController.php
namespace AppBundle\Controller\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

class DashboardController extends Controller
{
    /** @DI\Inject("security.authorization_checker") */
    private $_authorizationChecker;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="employee_dashboard",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function indexAction()
    {
        if( $this->_authorizationChecker->isGranted('ROLE_ADMIN') ) {
            return $this->redirectToRoute('employee_read');
        } else {
            return $this->redirectToRoute('operator_read');
        }
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/test",
     *      name="employee_test",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function testAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->_manager = $this->getDoctrine()->getManager();

        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated('SM-0001');

        $content = \SyncBundle\Tests\SyncData\BankingMachine\Replenishment::getData();

        $request = \Symfony\Component\HttpFoundation\Request::create(
            '/test', 'POST',
            ['some' => 'shit'], [], [], [],
            $content
        );

        try {
            $serializedBankingMachineSync = $this->get('sync.banking_machine.sync.validator.structure')
                ->getBankingMachineSyncIfValid($request);

            $bankingMachineSync = $this->get('app.serializer.banking_machine_sync')
                ->syncUnserializeObject($serializedBankingMachineSync);

            $bankingMachineSync->setSyncType('sync_get_banking_machines_replenishments');

            $serializedReplenishments = $this->get('sync.banking_machine.sync.validator.structure')
                ->getReplenishmentsIfValid($request);

            $replenishments = $this->get('app.serializer.replenishment')
                ->syncUnserializeArray($serializedReplenishments);
        } catch(\RuntimeException $e) {
            return new \Symfony\Component\HttpFoundation\Response($e->getMessage());
        }

        $this->_manager->getConnection()->beginTransaction();

        try{
            $bankingMachineSync = $this->get('sync.banking_machine.sync.handler')
                ->handleBankingMachineSyncDataTest($bankingMachine, $bankingMachineSync);

            file_put_contents('/media/kidbinary/Data/web/fintech/app/logs/debug.txt', print_r($replenishments, TRUE));

            $replenishments = $this->get('sync.banking_machine.sync.handler')
                ->handleReplenishmentDataTest($bankingMachine, $bankingMachineSync, $replenishments);

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( \Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new \Exception('Database Error: ' . $e->getMessage());
        }

        echo '<pre>';
        \Doctrine\Common\Util\Debug::dump($replenishments, 5);
        echo '</pre>';

        return new \Symfony\Component\HttpFoundation\Response('ok');
    }
}
