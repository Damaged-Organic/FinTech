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

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Serializer\OperatorSerializer,
    AppBundle\Serializer\AccountGroupSerializer,
    AppBundle\Serializer\BankingMachineSerializer;

use SyncBundle\EventListener\Security\Markers\AuthorizationMarkerInterface;

class BankingMachineController extends Controller implements AuthorizationMarkerInterface
{
    use EntityFilter;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("sync.banking_machine.sync.formatter") */
    private $_formatter;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}",
     *      name = "sync_get_banking_machines",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesAction($serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $serialized = BankingMachineSerializer::syncSerializeObject($bankingMachine);

        $formattedData = $this->_formatter->formatRawData($serialized);

        // TODO: Data Recorder

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }

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

        $operators = $this->filterDeleted($bankingMachine->getOperators());

        $serialized = OperatorSerializer::syncSerializeArray($operators);

        $formattedData = $this->_formatter->formatRawData($serialized);

        // TODO: Data Recorder

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/account_groups",
     *      name = "sync_get_banking_machines_account_groups",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesAccountGroupsAction($serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $accountGroups = $this->filterDeleted($bankingMachine->getAccountGroups());

        $serialized = AccountGroupSerializer::syncSerializeArray($accountGroups);

        $formattedData = $this->_formatter->formatRawData($serialized);

        // TODO: Data Recorder

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }
}
