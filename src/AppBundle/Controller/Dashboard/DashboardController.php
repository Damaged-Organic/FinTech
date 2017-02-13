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
    public function testAction()
    {
        $test = [
            'transaction-at' => (new \DateTime)->format('Y-m-d H:i:s'),
            'operator' => [
                'id'        => 1,
                'full-name' => "First Test Test",
            ],
            'account-group' => [
                'id'   => 1,
                'name' => "First Test"
            ],
            'banknotes' => [
                [
                    'currency' => 'UAH',
                    'nominal'  => 5,
                    'quantity' => 10
                ],
                [
                    'currency' => 'UAH',
                    'nominal'  => 2,
                    'quantity' => 20
                ],
                [
                    'currency' => 'UAH',
                    'nominal'  => 1,
                    'quantity' => 50
                ]
            ]
        ];

        $replenishment = $this->get('app.serializer.replenishment')->syncUnserialize($test);

        if( $replenishment == FALSE )
            return new \Symfony\Component\HttpFoundation\Response('bitchy');

        echo '<pre>';
        var_dump($replenishment);
        echo '</pre>';

        return new \Symfony\Component\HttpFoundation\Response('ok');
    }
}
