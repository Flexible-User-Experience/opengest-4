<?php

namespace App\Admin\Purchase;

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
 * Class PurchaseInvoiceDueDateAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class PurchaseInvoiceDueDateAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Vencimientos de factura de compra';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'compras/vencimientos-factura';

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Vencimiento', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'purchaseInvoice',
                EntityType::class,
                [
                    'class' => SaleInvoice::class,
                    'label' => 'admin.label.purchase_invoice',
                    'required' => true,
                    'query_builder' => $this->rm->getPurchaseInvoiceRepository()->getFilteredByEnterpriseSortedByDateQB($this->getUserLogedEnterprise()),
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
                'purchaseInvoice',
                null,
                [
                    'label' => 'admin.label.purchase_invoice',
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
                    'label' => 'admin.label.date',
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
                'purchaseInvoice',
                null,
                [
                    'label' => 'admin.label.purchase_invoice',
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
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'admin.with.actions',
                ]
            )
        ;
    }
}
