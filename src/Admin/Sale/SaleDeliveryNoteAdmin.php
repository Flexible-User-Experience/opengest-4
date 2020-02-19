<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Entity\Sale\SaleInvoice;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class SaleDeliveryNoteAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleDeliveryNoteAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Albarà';
    protected $baseRoutePattern = 'vendes/albara';
    protected $datagridValues = array(
        '_sort_by' => 'date',
        '_sort_order' => 'DESC',
    );

    /**
     * @param FormMapper $formMapper
     *
     * @throws \Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->id($this->getSubject())) { // is edit mode
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'deliveryNoteNumber',
                    null,
                    array(
                        'label' => 'Número d\'albarà',
                        'required' => true,
                        'disabled' => true,
                    )
                )
                ->end()
            ;
        }
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'Data petició',
                    'format' => 'd/m/Y',
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
            ->with('Import', $this->getFormMdSuccessBoxArray(4))
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
            ->with('Factures', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'saleInvoices',
                EntityType::class,
                array(
                    'class' => SaleInvoice::class,
                    'label' => 'Factures',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder' => $this->rm->getSaleInvoiceRepository()->getFilteredByEnterpriseSortedByDateQB($this->getUserLogedEnterprise()),
                    'by_reference' => false,
                )
            )
            ->add(
                'wontBeInvoiced',
                CheckboxType::class,
                array(
                    'label' => 'No facturable',
                    'required' => false,
                )
            )
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
            $formMapper
                ->with('Albarà línies', $this->getFormMdSuccessBoxArray(12))
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
                'doctrine_orm_date',
                array(
                    'label' => 'Data albarà',
                    'field_type' => DatePickerType::class,
                )
            )
            ->add(
                'partner',
                'doctrine_orm_model_autocomplete',
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
                'deliveryNoteNumber',
                null,
                array(
                    'label' => 'Número albarà',
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
                'saleInvoices',
                null,
                array(
                    'label' => 'Factura',
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
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->orderBy('e.name', 'ASC')
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
        unset($this->listModes['mosaic']);
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $listMapper
                ->add(
                    'enterprise',
                    null,
                    array(
                        'label' => 'Empresa',
                    )
                )
            ;
        }
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
                'deliveryNoteNumber',
                null,
                array(
                    'label' => 'Número albarà',
                    'editable' => true,
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
                        'show' => array('template' => '::Admin/Buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
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
        $object->setDeliveryNoteNumber($this->getConfigurationPool()->getContainer()->get('app.delivery_note_manager')->getLastDeliveryNoteByenterprise($this->getUserLogedEnterprise()));
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

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $em->flush();
    }
}
