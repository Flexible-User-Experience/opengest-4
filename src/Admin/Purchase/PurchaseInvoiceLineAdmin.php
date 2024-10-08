<?php

namespace App\Admin\Purchase;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Purchase\PurchaseInvoice;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use App\Enum\ConstantsEnum;
use App\Enum\IvaEnum;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class PurchaseInvoiceLineAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class PurchaseInvoiceLineAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Líneas de factura de compra';

    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'compras/factura-linea';
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'purchaseInvoice',
            'purchaseInvoice.dateFormatted',
            'purchaseInvoice.partner',
            'purchaseItem',
            'description',
            'units',
            'priceUnit',
            'baseTotal',
            'total',
            'saleDeliveryNote',
            'vehicle',
            'operator',
            'costCenter',
        ];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.label.purchase_invoice_line', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'purchaseItem',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'label' => 'admin.label.purchase_item',
                    'required' => false,
                    'placeholder' => '---',
                    'btn_add' => false,
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                    'required' => false,
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                    'required' => false,
                    'scale' => 4,
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'admin.label.price_unit',
                    'required' => true,
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'admin.label.discount',
                    'required' => false,
                ]
            )
            ->add(
                'baseTotal',
                null,
                [
                    'label' => 'admin.label.base',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'iva',
                ChoiceType::class,
                [
                    'label' => 'IVA',
                    'required' => true,
                    'choices' => IvaEnum::getReversedEnumArray(),
                    'preferred_choices' => [$this->getIvaFromPartner()],
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'IRPF',
                    'required' => true,
                    'empty_data' => (string) ConstantsEnum::IRPF,
                    'attr' => [
                        'placeholder' => ConstantsEnum::IRPF,
                    ],
                    'data' => $this->getIrpfFromPartner(),
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'vehicle',
                EntityType::class,
                [
                    'class' => Vehicle::class,
                    'label' => 'admin.label.vehicle',
                    'required' => false,
                    'placeholder' => '---',
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'operator',
                EntityType::class,
                [
                    'class' => Operator::class,
                    'label' => 'admin.label.operator',
                    'required' => false,
                    'placeholder' => '---',
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'saleDeliveryNote',
                EntityType::class,
                [
                    'class' => SaleDeliveryNote::class,
                    'label' => 'admin.with.delivery_note',
                    'required' => false,
                    'placeholder' => '---',
                ]
            )
            ->add(
                'costCenter',
                EntityType::class,
                [
                    'class' => CostCenter::class,
                    'label' => 'admin.label.cost_center',
                    'required' => false,
                    'placeholder' => '---',
                    'query_builder' => $this->rm->getCostCenterRepository()->getEnabledSortedByNameQB(),
                ]
            )
        ;
        if ($this->getCode() == $this->getRootCode()) {
            $formMapper
                ->add(
                    'purchaseInvoice',
                    EntityType::class,
                    [
                        'class' => PurchaseInvoice::class,
                        'label' => false,
                        'required' => true,
                    ]
                )
            ;
        } else {
            $formMapper
                ->add(
                    'purchaseInvoice',
                    EntityType::class,
                    [
                        'class' => PurchaseInvoice::class,
                        'label' => false,
                        'required' => true,
                        'attr' => [
                            'hidden' => 'true',
                        ],
                    ]
                )
            ;
        }
        $formMapper
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
                'purchaseInvoice.date',
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
                    'show_filter' => true,
                ]
            )
            ->add(
                'purchaseInvoice.partner',
                null,
                [
                    'label' => 'admin.label.supplier',
                    'admin_code' => 'app.admin.partner',
                    'show_filter' => true,
                ]
            )
            ->add(
                'purchaseItem',
                null,
                [
                    'label' => 'admin.label.item',
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'admin.label.price_unit',
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
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
                'iva',
                null,
                [
                    'label' => 'admin.label.IVA',
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'admin.label.IRPF',
                ]
            )
            ->add(
                'saleDeliveryNote',
                null,
                [
                    'label' => 'admin.with.delivery_note',
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
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'costCenter',
                null,
                [
                    'label' => 'admin.label.cost_center',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.purchaseInvoice', 'pi')
                ->where('pi.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
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
                'purchaseInvoice.dateFormatted',
                null,
                [
                    'label' => 'admin.label.date',
                ]
            )
            ->add(
                'purchaseInvoice.partner',
                null,
                [
                    'label' => 'admin.label.supplier',
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'purchaseItem',
                null,
                [
                    'label' => 'admin.label.item',
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
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'admin.label.price_unit',
                ]
            )
            ->add(
                'baseTotal',
                null,
                [
                    'label' => 'admin.label.base',
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                ]
            )
            ->add(
                'saleDeliveryNote',
                null,
                [
                    'label' => 'admin.with.delivery_note',
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
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'costCenter',
                null,
                [
                    'label' => 'admin.label.cost_center',
                ]
            )
//            ->add(
//                '_action',
//                'actions',
//                [
//                    'actions' => [
//                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
//                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
//                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
//                    ],
//                    'label' => 'Accions',
//                ]
//            )
        ;
    }

    private function getIrpfFromPartner()
    {
        /** @var PurchaseInvoiceLine $purchaseInvoiceLine */
        $purchaseInvoiceLine = $this->getSubject();
        if (!$this->id($purchaseInvoiceLine)) {
            return $this->getSubject()->getPurchaseInvoice()?->getPartner()->getDefaultIrpf() ?: 0;
        } else {
            return $purchaseInvoiceLine->getIrpf();
        }
    }

    private function getIvaFromPartner()
    {
        $purchaseInvoice = $this->getSubject()->getPurchaseInvoice();
        if ($purchaseInvoice) {
            return $this->getSubject()->getPurchaseInvoice()?->getPartner()->getDefaultIva() ?: 0;
        }
    }
}
