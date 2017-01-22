<?php
// src/SyncBundle/Controller/BankingMachineController.php
namespace SyncBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\HttpKernel\Exception\FatalErrorException,
    Symfony\Component\Security\Core\Exception\BadCredentialsException;

use JMS\DiExtraBundle\Annotation as DI;

use SyncBundle\Controller\Utility\Interfaces\Markers\AuthorizationMarkerInterface;

class BankingMachineController extends Controller implements
    AuthorizationMarkerInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/operators",
     *      name = "sync_get_banking_machines_operators",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesOperatorsAction($serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $operators = $bankingMachine->getOperators();

        return new Response(json_encode('Done', JSON_UNESCAPED_UNICODE), 200);
    }
}
