<?php
// AppBundle/Controller/Binding/EmployeeController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

use AppBundle\Entity\Employee\Employee,
    AppBundle\Security\Authorization\Voter\EmployeeVoter;

use AppBundle\Entity\Organization\Organization;

class EmployeeController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/employee/update/{objectId}/bounded/{objectClass}",
     *      name="employee_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

        if( !$employee )
            throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

        if( !$this->isGranted(EmployeeVoter::EMPLOYEE_READ, $employee) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs
            ->add('employee_read')
            ->add('employee_update', [
                'id' => $objectId
            ], $this->_translator->trans('employee_bounded', [], 'routes'))
        ;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Organization:show', [
                    'objectClass' => $this->getObjectClassName($employee),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('organization_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Employee/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'employee'    => $employee
        ]);
    }
}
