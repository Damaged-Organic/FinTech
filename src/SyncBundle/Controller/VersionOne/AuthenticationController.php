<?php
// src/SyncBundle/Controller/VersionOne/AuthenticationController.php
namespace SyncBundle\Controller\VersionOne;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\HttpKernel\Exception\FatalErrorException,
    Symfony\Component\Security\Core\Exception\BadCredentialsException;

use JMS\DiExtraBundle\Annotation as DI;

class AuthenticationController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("sync.banking_machine.security.authentication") */
    private $_authentication;

    /** @DI\Inject("sync.banking_machine.security.authorization") */
    private $_authorization;

    /** @DI\Inject("sync.banking_machine.sync.formatter") */
    private $_formatter;

    /**
     * @Method({"POST"})
     * @Route(
     *      "/authentication/checkin/banking_machines/{serial}",
     *      name = "sync_authentication_banking_machines_checkin",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function checkinBankingMachinesAction(Request $request, $serial)
    {
        $repository = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine');

        $bankingMachine = $repository->findOneBy(['serial' => $serial]);

        if( !$bankingMachine )
            throw new NotFoundHttpException('Banking Machine not found');

        if( !$this->_authentication->isAuthenticated($request, $bankingMachine) )
            throw new AccessDeniedHttpException('Authentication failed');

        $token        = $this->_authorization->generateToken();
        $encodedToken = $this->_authorization->encodeToken($token);

        $bankingMachine->setApiTokenAndExpirationTime($encodedToken);

        $this->_manager->persist($bankingMachine);
        $this->_manager->flush();

        $formattedData = $this->_formatter->formatRawData(['token' => $token]);

        $encodedData = json_encode($formattedData, JSON_UNESCAPED_UNICODE);

        return new Response($encodedData, 200);
    }

    public function checkoutBankingServerAction()
    {
        // Stub
    }
}
