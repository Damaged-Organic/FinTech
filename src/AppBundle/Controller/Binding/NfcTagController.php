<?php
// src/AppBundle/Controller/Binding/NfcTagController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

use AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Service\Security\NfcTagBoundlessAccess;

use AppBundle\Entity\Operator\Operator,
    AppBundle\Security\Authorization\Voter\OperatorVoter;

class NfcTagController extends Controller implements UserRoleListInterface
{
    
}
