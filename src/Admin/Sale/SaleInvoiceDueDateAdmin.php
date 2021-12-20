<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleInvoice;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleInvoiceDueDateAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class SaleInvoiceDueDateAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Vencimientos de factura';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'ventas/vencimientos-factura';

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Vencimiento', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'saleInvoice',
                EntityType::class,
                [
                    'class' => SaleInvoice::class,
                    'label' => 'admin.label.sale_invoice',
                    'required' => true,
                    'query_builder' => $this->rm->getSaleInvoiceRepository()->getRecentFilteredByEnterpriseSortedByDateQB($this->getUserLogedEnterprise()),
                    'attr' => [
                        'hidden' => 'true',
                    ],
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'admin.label.date',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.amount',
                    'required' => true,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'saleInvoice',
                null,
                [
                    'label' => 'admin.label.sale_invoice',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'date',
                DateRangeFilter::class,
                [
                    'label' => '1r día nómina',
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'Desde',
                            'format' => 'dd/MM/yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'Hasta',
                            'format' => 'dd/MM/yyyy',
                        ],
                    ],
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'saleInvoice',
                null,
                [
                    'label' => 'admin.label.sale_invoice',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.amount',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.date',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
