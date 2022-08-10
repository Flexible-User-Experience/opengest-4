<?php

namespace App\Admin\Purchase;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Partner\PartnerDeliveryAddress;
use App\Entity\Partner\PartnerType;
use App\Entity\Setting\City;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class PurchaseInvoiceAdmin.
 *
 * @category    Admin
 */
class PurchaseInvoiceAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Facturas de compra';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'compras/facturas';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'date';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('show')
        ;
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'series',
            'invoiceNumber',
            'dateFormatted',
            'partner.code',
            'partner.name',
            'partner.cifNif',
            'discountFormatted',
            'baseTotalFormatted',
            'ivaFormatted',
            'irpfFormatted',
            'totalFormatted',
        ];
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'invoiceNumber',
                null,
                [
                    'label' => 'admin.label.invoice_number_long',
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'partner',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'disabled' => true,
                    'label' => 'admin.label.partner',
                    'callback' => function ($admin, $property, $value) {
                        /** @var Admin $admin */
                        $datagrid = $admin->getDatagrid();
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();
                        $queryBuilder
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                            ->andWhere($queryBuilder->getRootAliases()[0].'.type = :type')
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enabled = :enabled')
                            ->setParameter('enterprise', $this->getUserLogedEnterprise())
                            ->setParameter('type', $this->getModelManager()->find(PartnerType::class, 1))
                            ->setParameter('enabled', true)
                        ;
                        $datagrid->setValue($property, null, $value);
                    },
                ],
                [
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'partnerName',
                null,
                [
                    'label' => 'admin.label.partner',
                    'required' => true,
                ]
            )
            ->add(
                'partnerCifNif',
                null,
                [
                    'label' => 'CIF/NIF',
                    'required' => true,
                ]
            )
            ->add(
                'partnerMainAddress',
                null,
                [
                    'label' => 'admin.label.main_address',
                    'required' => true,
                ]
            )
            ->add(
                'partnerMainCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'admin.label.main_city',
                    'required' => true,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ;
        if ($this->id($this->getSubject())) { // is edit mode
            if ($this->getSubject()->getPartnerMainCity()) {
                $formMapper
                    ->add(
                        'partnerMainCity.province.countryName',
                        null,
                        [
                            'label' => 'admin.label.country_name',
                            'required' => false,
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'partnerMainCity.province',
                        null,
                        [
                            'label' => 'admin.label.province',
                            'required' => false,
                            'disabled' => true,
                        ]
                    )
                ;
            }
        }
        $formMapper
            ->end()
            ->with('admin.label.amount', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'baseTotal',
                null,
                [
                    'label' => 'admin.label.base_amount',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ;
        if ($this->getSubject()->getIva21() > 0) {
            $formMapper
                ->add(
                    'iva21',
                    null,
                    [
                        'label' => 'admin.label.iva21_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        if ($this->getSubject()->getIva10() > 0) {
            $formMapper
                ->add(
                    'iva10',
                    null,
                    [
                        'label' => 'admin.label.iva10_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        if ($this->getSubject()->getIva4() > 0) {
            $formMapper
                ->add(
                    'iva4',
                    null,
                    [
                        'label' => 'admin.label.iva4_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        if ($this->getSubject()->getIva0() > 0) {
            $formMapper
                ->add(
                    'iva0',
                    null,
                    [
                        'label' => 'admin.label.iva0_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        $formMapper
            ->add(
                'iva',
                null,
                [
                    'label' => 'admin.label.iva_amount',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'admin.label.irpf_amount',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'partner.payDay1',
                null,
                [
                    'label' => 'admin.label.pay_day_1',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode
            $formMapper
                ->with('admin.label.delivery_address', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'deliveryAddress',
                    EntityType::class,
                    [
                        'label' => 'admin.label.delivery_address',
                        'required' => false,
                        'class' => PartnerDeliveryAddress::class,
                        'query_builder' => $this->rm->getPartnerDeliveryAddressRepository()->getFilteredByPartnerSortedByNameQB($this->getSubject()->getPartner()->getId()),
                        'placeholder' => '--- Seleccione una dirección de envio alternativa ---',
                    ]
                )
                ->end()
                ->with('observations', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'observations',
                    null,
                    [
                        'required' => false,
                        'label' => false,
                    ]
                )
                ->end()
            ->with('admin.label.due_dates', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'saleInvoiceDueDates',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'invoiceNumber',
                null,
                [
                    'label' => 'admin.label.invoice_number',
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
            ->add(
                'partner',
                ModelFilter::class,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                        'property' => 'name',
                        'callback' => $this->partnerModelAutocompleteCallback(),
                    ],
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.partner', 'p')
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'invoiceNumber',
                null,
                [
                    'label' => 'admin.label.invoice_number',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'partner.code',
                null,
                [
                    'label' => 'admin.label.partner_code',
                ]
            )
            ->add(
                'partner',
                null,
                [
                    'label' => 'admin.label.partner',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'partner']],
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'deliveryAddress',
                null,
                [
                    'label' => 'admin.label.delivery_address',
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                    'template' => 'admin/cells/list__cell_total_currency_number.html.twig',
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
