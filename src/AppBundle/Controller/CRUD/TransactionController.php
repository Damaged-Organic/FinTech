<?php
// src/AppBundle/Controller/CRUD/TransactionController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Transaction\Transaction;
    // AppBundle\Form\Type\TransactionType,
    // AppBundle\Security\Authorization\Voter\TransactionVoter,
    // AppBundle\Service\Security\TransactionBoundlessAccess;

class TransactionController extends Controller implements UserRoleListInterface
{
    use EntityFilter;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

    // /** @DI\Inject("app.security.account_boundless_access") */
    // private $_accountBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/transaction/{id}",
     *      name="transaction_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {

    }
}
