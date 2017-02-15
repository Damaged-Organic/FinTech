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
        $content = \SyncBundle\Tests\SyncData\BankingMachine\Replenishment::getData();

        $request = \Symfony\Component\HttpFoundation\Request::create(
            '/test', 'POST', array('some' => 'shit'),
            [], [], [],
            $content
        );

        try {
            $serializedReplenishments = $this->get('sync.banking_machine.sync.validator.structure')->getReplenishmentsIfValid($request);
        } catch(\RuntimeException $e) {
            return new \Symfony\Component\HttpFoundation\Response($e->getMessage());
        }

        try {
            $replenishment = $this->get('app.serializer.replenishment')->syncUnserializeArray($serializedReplenishments);
        } catch(\RuntimeException $e) {
            return new \Symfony\Component\HttpFoundation\Response($e->getMessage());
        }

        if( $replenishment == FALSE )
            return new \Symfony\Component\HttpFoundation\Response('bitchy');

        echo '<pre>';
        var_dump($replenishment);
        echo '</pre>';

        return new \Symfony\Component\HttpFoundation\Response('ok');
    }
}
