<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Enum\UserRolesEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleDeliveryNoteToInvoiceCustomAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class SaleDeliveryNoteToInvoiceCustomAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Albaran para facturar';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/albaran-para-facturar';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_app_sale_saledeliverynote_to_invoice_custom';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'id';
    }

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('generateInvoices', 'generate-invoices')
        ;
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'dateToString',
            'saleRequestNumber',
            'isInvoiced',
            'saleInvoice',
            'order',
            'deliveryNoteReference',
            'saleServiceTariff',
            'serviceDescription',
            'partner.name',
            'partner.cifNif',
            'place',
            'buildingSite',
            'vehicle',
            'secondaryVehicle',
            'activityLine',
            'operator',
            'miniumHours',
            'hourPriceFormatted',
            'displacement',
            'miniumHolidayHours',
            'totalLinesFormatted',
            'discountFormatted',
            'baseAmountFormatted',
            'finalTotalFormatted',
            'observations',
        ];
    }

    public function configureBatchActions(array $actions): array
    {
        $newActions = [];
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
            $newActions['generateSaleInvoiceFromDeliveryNotes'] = [
                'label' => 'admin.action.generate_invoice_from_selected',
                'ask_confirmation' => false,
            ];
            $newActions['generateStandardPrint'] = [
                'label' => 'admin.action.generate_standard_print_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $newActions['generateDriverPrint'] = [
                'label' => 'admin.action.generate_driver_print_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $newActions['generateStandardMail'] = [
                'label' => 'admin.action.generate_standard_mail_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $newActions['generateDriverMail'] = [
                'label' => 'admin.action.generate_driver_mail_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $newActions['deliveryNotesByClient'] = [
                'label' => 'admin.action.generate_delivery_notes_by_client',
                'ask_confirmation' => false,
            ];
            $newActions['deliveryNotesList'] = [
                'label' => 'admin.action.generate_delivery_notes_list',
                'ask_confirmation' => false,
            ];
        }
        unset($actions['delete']);

        return array_merge($newActions, $actions);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
            'id',
            null,
                [
                    'label' => 'admin.label.delivery_note_number',
                    'show_filter' => true,
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
                    'show_filter' => true,
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
                    'show_filter' => true,
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
                'buildingSite',
                null,
                [
                    'label' => 'admin.label.partner_building_site',
                ]
            )
            ->add(
                'order',
                null,
                [
                    'label' => 'admin.label.order',
                ]
            )
            ->add(
                'serviceDescription',
                null,
                [
                    'label' => 'admin.label.service_description',
                ]
            )
            ->add(
                'saleServiceTariff',
                null,
                [
                    'label' => 'admin.label.tonnage',
                ]
            )
            ->add(
                'deliveryNoteReference',
                null,
                [
                    'label' => 'admin.label.delivery_note_reference',
                ]
            )
            ->add(
                'baseAmount',
                null,
                [
                    'label' => 'admin.label.base_amount',
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'admin.label.discount',
                ]
            )
            ->add(
                'activityLine',
                null,
                [
                    'label' => 'admin.label.activity_line',
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
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => Operator::class,
                        'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ],                ]
            )
            ->add(
                'collectionDocument',
                null,
                [
                    'label' => 'admin.label.collection_document_type',
                ]
            )
            ->add(
                'collectionTerm',
                null,
                [
                    'label' => 'admin.label.collection_term_1',
                ]
            )
            ->add(
                'collectionTerm2',
                null,
                [
                    'label' => 'admin.label.collection_term_2',
                ]
            )
            ->add(
                'collectionTerm3',
                null,
                [
                    'label' => 'admin.label.collection_term_3',
                ]
            )
            ->add(
                'wontBeInvoiced',
                null,
                [
                    'label' => 'admin.label.no_invoice',
                ]
            )
            ->add(
                'isInvoiced',
                null,
                [
                    'label' => 'admin.label.invoiced',
                    'show_filter' => true,
                ]
            )
            ->add(
                'printed',
                null,
                [
                    'label' => 'admin.label.printed',
                ]
            )
            ->add(
                'place',
                null,
                [
                    'label' => 'admin.label.place',
                ]
            )
        ;
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        $filterValues['isInvoiced'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_NO,
        ];
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->leftJoin($queryBuilder->getRootAliases()[0].'.partner', 'pa')
            ->orderBy($queryBuilder->getRootAliases()[0].'.date', 'DESC')
        ;
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'admin.label.delivery_note_number',
                    'template' => 'admin/cells/list__cell_sale_delivery_note.html.twig',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.delivery_note_date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'saleRequest.serviceTime',
                FieldDescriptionInterface::TYPE_TIME,
                [
                    'label' => 'admin.label.service_time',
                    'format' => 'H:i',
                ]
            )
            ->add(
                'partner',
                null,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'id'],
                    'sort_parent_association_mappings' => [['fieldName' => 'partner']],
                ]
            )
            ->add(
                'buildingSite',
                null,
                [
                    'label' => 'admin.label.partner_building_site',
                ]
            )
            ->add(
                'order',
                null,
                [
                    'label' => 'order',
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
                'vehicle.tonnage',
                null,
                [
                    'label' => 'admin.label.tonnage',
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
                'baseAmount',
                null,
                [
                    'label' => 'admin.label.base_amount',
                    'template' => 'admin/cells/list__cell_base_amount_currency_number.html.twig',
                ]
            )
            ->add(
                'isInvoiced',
                'boolean',
                [
                    'label' => 'admin.label.invoiced',
                    'transform' => true,
                ]
            )
            ->add(
                'wontBeInvoiced',
                'boolean',
                [
                    'label' => 'admin.label.no_invoice',
                    'transform' => true,
                ]
            )
            ->add(
                'saleInvoice',
                null,
                [
                    'template' => 'admin/cells/list__cell_sale_invoice_sale_delivery_note.html.twig',
                    'label' => 'admin.with.sale_invoice',
                ]
            )
        ;
    }

    /**
     * @param SaleDeliveryNote $object
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $partner = $object->getPartner();
        if (!$object->getCollectionDocument()) {
            $object->setCollectionDocument($partner->getCollectionDocumentType());
        }
        if (!$object->getCollectionTerm()) {
            $object->setCollectionTerm($partner->getCollectionTerm1());
        }
        if (!$object->getCollectionTerm2()) {
            $object->setCollectionTerm2($partner->getCollectionTerm2());
        }
        if (!$object->getCollectionTerm3()) {
            $object->setCollectionTerm3($partner->getCollectionTerm3());
        }
        $availableIds = $this->dnm->getAvailableIdsByEnterprise($partner->getEnterprise());
        if (count($availableIds) > 0) {
            $metadata = $this->em->getClassMetadata(SaleDeliveryNote::class);
            $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
            $object->setId(array_values($availableIds)[0]);
        }
    }

    /**
     * @param SaleDeliveryNote $object
     */
    public function preUpdate($object): void
    {
        /** @var SaleDeliveryNote $originalObject */
        $originalObject = $this->em->getUnitOfWork()->getOriginalEntityData($object);
        if ($object->getPartner()->getId() != $originalObject['partner_id']) {
            $partner = $object->getPartner();
            $object->setCollectionTerm($partner->getCollectionTerm1());
            $object->setCollectionTerm2($partner->getCollectionTerm2());
            $object->setCollectionTerm3($partner->getCollectionTerm3());
            $object->setCollectionDocument($partner->getCollectionDocumentType());
        }
    }

    /**
     * @param SaleDeliveryNote $object
     */
    public function postUpdate($object): void
    {
        $totalPrice = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($object->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $base = $deliveryNoteLine->getUnits() * $deliveryNoteLine->getPriceUnit() * (1 - $deliveryNoteLine->getDiscount() / 100);
            $deliveryNoteLine->setTotal($base);
            $subtotal = $deliveryNoteLine->getTotal();
            $totalPrice = $totalPrice + $subtotal;
        }
        $object->setBaseAmount($totalPrice * (1 - $object->getDiscount() / 100));
        $saleInvoice = $object->getSaleInvoice();
        if ($saleInvoice) {
            $saleInvoice->setCollectionDocumentType($object->getCollectionDocument());
            //If invoiced, set same collectionTerms and collectionDocuments for all the delivery notes belonging to this invoice
            /** @var SaleDeliveryNote $deliveryNote */
            foreach ($saleInvoice->getDeliveryNotes() as $deliveryNote) {
                if ($deliveryNote->getId() !== $object->getId()) {
                    $deliveryNote->setCollectionDocument($object->getCollectionDocument());
                    $deliveryNote->setCollectionTerm($object->getCollectionTerm());
                    $deliveryNote->setCollectionTerm2($object->getCollectionTerm2());
                    $deliveryNote->setCollectionTerm3($object->getCollectionTerm3());
                }
            }
            $this->im->calculateInvoiceImportsFromDeliveryNotes($saleInvoice, $saleInvoice->getDeliveryNotes());
            $saleInvoice->setSaleInvoiceDueDates(new ArrayCollection());
            $this->im->createDueDatesFromSaleInvoice($saleInvoice);
        }

        $this->em->flush();
    }
}
