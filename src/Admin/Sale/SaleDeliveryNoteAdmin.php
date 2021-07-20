<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Operator\Operator;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SaleDeliveryNoteAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleDeliveryNoteAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Albarà';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/albara';

    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'date',
        '_sort_order' => 'DESC',
    );

    /**
     * Methods.
     */

    /**
     * @param FormMapper $formMapper
     *
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->id($this->getSubject())) { // is edit mode
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'id',
                    null,
                    array(
                        'label' => 'Id d\'albarà',
                        'required' => true,
                        'disabled' => true,
                    )
                )
                ->end()
            ;
        }
        if ($this->getSubject()->getSaleRequestHasDeliveryNotes()->isEmpty() == false) {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'saleRequestNumber',
                    TextType::class,
                    array(
                        'label' => 'Número de petició',
                        'required' => false,
                        'disabled' => true,
                    )
                )
                ->end()
            ;
        }
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'Data',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                )
            )
            ->add(
                'partner',
                ModelAutocompleteType::class,
                array(
                    'property' => 'name',
                    'label' => 'Client',
                    'required' => true,
                    'callback' => function ($admin, $property, $value) {
                        /** @var Admin $admin */
                        $datagrid = $admin->getDatagrid();
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();
                        $queryBuilder
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                            ->setParameter('enterprise', $this->getUserLogedEnterprise())
                        ;
                        $datagrid->setValue($property, null, $value);
                    },
                )
            )
            ->add(
                'cifNif',
                TextType::class,
                array(
                    'label' => 'CIF',
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                    'help' => '<i id="cif-nif-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                )
            )
            ->add(
                'deliveryNoteReference',
                null,
                array(
                    'label' => 'Referencia d\'albarà',
                    'required' => true,
                    'disabled' => false,
                )
            )
            ->add(
                'buildingSite',
                EntityType::class,
                array(
                    'class' => PartnerBuildingSite::class,
                    'label' => 'Obra',
                    'required' => false,
                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'order',
                EntityType::class,
                array(
                    'class' => PartnerOrder::class,
                    'label' => 'Comanda',
                    'required' => false,
                    'query_builder' => $this->rm->getPartnerOrderRepository()->getEnabledSortedByNumberQB(),
                )
            )
            ->end()
            ->with('Service', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'saleServiceTariff',
                EntityType::class,
                array(
                    'class' => SaleServiceTariff::class,
                    'label' => 'admin.label.sale_serivce_tariff',
                    'required' => true,
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'vehicle',
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'label' => 'admin.label.vehicle',
                    'required' => false,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'secondaryVehicle',
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'label' => 'admin.label.secondary_vehicle',
                    'required' => false,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'operator',
                EntityType::class,
                array(
                    'class' => Operator::class,
                    'label' => 'admin.label.operator',
                    'required' => false,
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'serviceDescription',
                TextareaType::class,
                array(
                    'label' => 'Descripció servei',
                    'required' => true,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ),
                )
            )
            ->add(
                'place',
                TextareaType::class,
                array(
                    'label' => 'Lloc',
                    'required' => false,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 3,
                    ),
                )
            )
            ->end()
            ->with('Import', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'baseAmount',
                null,
                array(
                    'label' => 'Import base',
                    'required' => true,
                    'disabled' => true,
                )
            )
            ->add(
                'discount',
                null,
                array(
                    'label' => 'Descompte',
                    'required' => false,
                )
            )
            ->add(
                'collectionDocument',
                EntityType::class,
                array(
                    'class' => CollectionDocumentType::class,
                    'label' => 'Tipus document cobrament',
                    'required' => false,
                    'query_builder' => $this->rm->getCollectionDocumentTypeRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'collectionTerm',
                null,
                array(
                    'label' => 'Venciment (dies)',
                    'required' => false,
                )
            )
            ->add(
                'activityLine',
                EntityType::class,
                array(
                    'class' => ActivityLine::class,
                    'label' => 'Línia d\'activitat',
                    'required' => false,
                    'query_builder' => $this->rm->getActivityLineRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->end()
        ;
        if ($this->getSubject()->getSaleRequestHasDeliveryNotes()->isEmpty() == false) {
            $formMapper
                ->with('Tarifa', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'miniumHours',
                    NumberType::class,
                    array(
                        'label' => 'admin.label.minimum_hours',
                        'disabled' => true
                    )
                )
                ->add(
                    'hourPrice',
                    NumberType::class,
                    array(
                        'label' => 'admin.label.price_hour',
                        'disabled' => true
                    )
                )
                ->add(
                    'displacement',
                    NumberType::class,
                    array(
                        'label' => 'admin.label.displacement',
                        'disabled' => true
                    )
                )
                ->add(
                    'miniumHolidayHours',
                    NumberType::class,
                    array(
                        'label' => 'admin.label.minimum_holiday_hours',
                        'disabled' => true
                    )
                )
                ->add(
                    'increaseForHolidays',
                    NumberType::class,
                    array(
                        'label' => 'admin.label.increase_for_holidays',
                        'disabled' => true
                    )
                )
                ->add(
                    'increaseForHolidaysPercentage',
                    PercentType::class,
                    array(
                        'label' => 'admin.label.increase_for_holidays_percentage',
                        'disabled' => true
                    )
                )
                ->end()
                ->with('admin.label.contact', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'contactPersonName',
                    TextType::class,
                    array(
                        'label' => 'admin.label.contact_person_name',
                        'disabled' => true
                    )
                )
                ->add(
                    'contactPersonPhone',
                    TextType::class,
                    array(
                        'label' => 'admin.label.contact_person_phone',
                        'disabled' => true
                    )
                )
                ->end();
        }
        $formMapper
            ->with('Factures', $this->getFormMdSuccessBoxArray(3))
//            ->add(
//                'saleInvoices',
//                EntityType::class,
//                array(
//                    'class' => SaleInvoice::class,
//                    'label' => 'Factures',
//                    'required' => false,
//                    'multiple' => true,
//                    'expanded' => true,
//                    'query_builder' => $this->rm->getSaleInvoiceRepository()->getFilteredByEnterpriseSortedByDateQB($this->getUserLogedEnterprise()),
////                    'by_reference' => false,
//                )
//            )
            ->add(
                'wontBeInvoiced',
                CheckboxType::class,
                array(
                    'label' => 'No facturable',
                    'required' => false,
                )
            )
            ->end()
            ->with('Altres', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'observations',
                TextareaType::class,
                array(
                    'label' => 'Observacions',
                    'required' => false,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 3,
                    ),
                )
            )
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
            $formMapper
                ->with('Línies', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'saleDeliveryNoteLines',
                    CollectionType::class,
                    array(
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->end()
            ;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $datagridMapper
                ->add(
                    'enterprise',
                    null,
                    array(
                        'label' => 'Empresa',
                    )
                )
            ;
        }
        $datagridMapper
            ->add(
                'date',
                DateFilter::class,
                array(
                    'label' => 'Data albarà',
                    'field_type' => DatePickerType::class,
                )
            )
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                array(
                    'label' => 'Client',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'buildingSite',
                null,
                array(
                    'label' => 'Obra',
                )
            )
            ->add(
                'order',
                null,
                array(
                    'label' => 'Comanda',
                )
            )
            ->add(
                'deliveryNoteReference',
                null,
                array(
                    'label' => 'Referencia d\'albarà',
                )
            )
            ->add(
                'baseAmount',
                null,
                array(
                    'label' => 'Import base',
                )
            )
            ->add(
                'discount',
                null,
                array(
                    'label' => 'Descompte',
                )
            )
            ->add(
                'collectionTerm',
                null,
                array(
                    'label' => 'Venciment',
                )
            )
            ->add(
                'collectionDocument',
                null,
                array(
                    'label' => 'Tipus document cobrament',
                )
            )
            ->add(
                'activityLine',
                null,
                array(
                    'label' => 'Línia activitat',
                )
            )
            ->add(
                'wontBeInvoiced',
                null,
                array(
                    'label' => 'No facturable',
                )
            )
        ;
    }

    /**
     * @param string $context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = parent::createQuery($context);
        $queryBuilder
//            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->leftJoin($queryBuilder->getRootAliases()[0].'.partner', 'pa')
//            ->orderBy('e.name', 'ASC')
        ;
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
//        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
//            $listMapper
//                ->add(
//                    'enterprise',
//                    null,
//                    array(
//                        'label' => 'Empresa',
//                    )
//                )
//            ;
//        }
        $listMapper
            ->add(
                'date',
                null,
                array(
                    'label' => 'Data albarà',
                    'format' => 'd/m/Y',
                )
            )
            ->add(
                'deliveryNoteReference',
                null,
                array(
                    'label' => 'Referència d\'albarà'
                )
            )
            ->add(
                'partner',
                null,
                array(
                    'label' => 'Client',
                )
            )
            ->add(
                'baseAmount',
                null,
                array(
                    'label' => 'Import base',
                    'editable' => false,
                )
            )
            ->add(
                'wontBeInvoiced',
                null,
                array(
                    'label' => 'No facturable',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
            )
        ;
    }

    /**
     * @param SaleDeliveryNote $object
     *
     * @throws NonUniqueResultException
     */
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $object->setDeliveryNoteReference($this->dnm->getLastDeliveryNoteByenterprise($this->getUserLogedEnterprise()));
    }

    /**
     * @param SaleDeliveryNote $object
     */
    public function postUpdate($object)
    {
        $totalPrice = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($object->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $base = $deliveryNoteLine->getUnits() * $deliveryNoteLine->getPriceUnit() - ($deliveryNoteLine->getDiscount() * $deliveryNoteLine->getPriceUnit() * $deliveryNoteLine->getUnits() / 100);
            $iva = $base * ($deliveryNoteLine->getIva() / 100);
            $irpf = $base * ($deliveryNoteLine->getIrpf() / 100);
            $deliveryNoteLine->setTotal($base + $iva - $irpf);
            $subtotal = $deliveryNoteLine->getTotal();
            $totalPrice = $totalPrice + $subtotal;
        }
        $object->setBaseAmount($totalPrice);

        $this->em->flush();
    }
}
