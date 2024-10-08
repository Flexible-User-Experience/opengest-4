<?php

namespace App\Admin\Payslip;

use App\Admin\AbstractBaseAdmin;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class PayslipLineConceptAdmin.
 *
 * @category    Admin
 */
class PayslipLineConceptAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'PayslipLineConcept';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'nominas/conceptos_linea';
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'isDeduction',
                CheckboxType::class,
                [
                    'required' => false,
                ]
            )
//            ->add(
//                'enabled',
//                CheckboxType::class,
//                [
//                    'label' => 'Actiu',
//                    'required' => false,
//                ]
//            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'isDeduction'
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'isDeduction',
                null,
                [
                    'editable' => true,
                ]
            )
//            ->add(
//                'enabled',
//                null,
//                [
//                    'label' => 'Activo',
//                    'editable' => true,
//                ]
//            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
