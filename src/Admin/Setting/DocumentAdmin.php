<?php

namespace App\Admin\Setting;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Vehicle\Vehicle;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class DocumentAdmin.
 *
 * @category Admin
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class DocumentAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Documentos';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'configuracion/documentos';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'description';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('show')
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'fileFile',
                FileType::class,
                [
                    'label' => 'admin.label.document',
//                    'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadTaxIdentificationNumberImage', 'taxIdentificationNumberImage'),
//                    'help_html' => true,
                    'required' => false,
                ]
            )
            ->add(
                'operator',
                EntityType::class,
                [
                    'class' => Operator::class,
                    'label' => 'admin.label.operator',
                    'required' => false,
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'vehicle',
                EntityType::class,
                [
                    'class' => Vehicle::class,
                    'label' => 'admin.label.vehicle',
                    'required' => false,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'enterprise',
                EntityType::class,
                [
                    'class' => Enterprise::class,
                    'label' => 'admin.label.enterprise',
                    'required' => false,
                    'query_builder' => $this->rm->getEnterpriseRepository()->getEnterprisesByUserQB($this->getUser()),
                ]
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled',
                    'required' => false,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => Vehicle::class,
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ],
                ]
            )
            ->add(
                'enterprise',
                null,
                [
                    'label' => 'admin.label.company',
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => Enterprise::class,
                        'query_builder' => $this->rm->getEnterpriseRepository()->getEnterprisesByUserQB($this->getUser()),
                    ],
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                    'editable' => true,
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                ]
            )
            ->add(
                'enterprise',
                null,
                [
                    'label' => 'admin.label.enterprise',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled',
                    'editable' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
