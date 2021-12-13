<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleRequestHasDeliveryNote;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleRequestHasDeliveryNoteAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleRequestHasDeliveryNoteAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Valoració petició-albarà';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/valoracio-peticio-albara';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'saleRequest',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'saleRequest',
                EntityType::class,
                [
                    'class' => SaleRequest::class,
                    'label' => 'Petició',
                    'required' => true,
                    'query_builder' => $this->rm->getSaleRequestRepository()->getFilteredByEnterpriseEnabledSortedByRequestDateQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'saleDeliveryNote',
                EntityType::class,
                [
                    'class' => SaleDeliveryNote::class,
                    'label' => 'Albarà',
                    'required' => true,
                    'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getFilteredByEnterpriseSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'reference',
                null,
                [
                    'label' => 'Referència',
                    'required' => false,
                ]
            )
            ->add(
                'ivaType',
                null,
                [
                    'label' => 'Tipus IVA',
                    'required' => false,
                ]
            )
            ->add(
                'retentionType',
                null,
                [
                    'label' => 'Tipus retenció',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Preus', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'totalHoursMorning',
                null,
                [
                    'label' => 'Total hores matí',
                    'required' => false,
                ]
            )
            ->add(
                'priceHourMorning',
                null,
                [
                    'label' => 'Preu hora matí',
                    'required' => false,
                ]
            )
            ->add(
                'amountMorning',
                null,
                [
                    'label' => 'Total matí',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'totalHoursAfternoon',
                null,
                [
                    'label' => 'Total hores tarda',
                    'required' => false,
                ]
            )
            ->add(
                'priceHourAfternoon',
                null,
                [
                    'label' => 'Preu hora tarda',
                    'required' => false,
                ]
            )
            ->add(
                'amountAfternoon',
                null,
                [
                    'label' => 'Total tarda',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'totalHoursNight',
                null,
                [
                    'label' => 'Total hores nit',
                    'required' => false,
                ]
            )
            ->add(
                'priceHourNight',
                null,
                [
                    'label' => 'Preu hora nit',
                    'required' => false,
                ]
            )
            ->add(
                'amountNight',
                null,
                [
                    'label' => 'Total nit',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'totalHoursEarlyMorning',
                null,
                [
                    'label' => 'Total hores matinada',
                    'required' => false,
                ]
            )
            ->add(
                'priceHourEarlyMorning',
                null,
                [
                    'label' => 'Preu hora matinada',
                    'required' => false,
                ]
            )
            ->add(
                'amountEarlyMorning',
                null,
                [
                    'label' => 'Total matinada',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'totalHoursDisplacement',
                null,
                [
                    'label' => 'Total hores desplaçament',
                    'required' => false,
                ]
            )
            ->add(
                'priceHourDisplacement',
                null,
                [
                    'label' => 'Preu hora desplaçament',
                    'required' => false,
                ]
            )
            ->add(
                'amountDisplacement',
                null,
                [
                    'label' => 'Total desplaçament',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'saleRequest',
                null,
                [],
                EntityType::class,
                [
                    'class' => SaleRequest::class,
                    'label' => 'Petició',
                    'query_builder' => $this->rm->getSaleRequestRepository()->getFilteredByEnterpriseEnabledSortedByRequestDateQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'saleDeliveryNote',
                null,
                [],
                EntityType::class,
                [
                    'class' => SaleDeliveryNote::class,
                    'label' => 'Albarà',
                    'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getFilteredByEnterpriseSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'totalHoursMorning',
                null,
                [
                    'label' => 'Total hores matí',
                ]
            )
            ->add(
                'priceHourMorning',
                null,
                [
                    'label' => 'Preu hora matí',
                ]
            )
            ->add(
                'amountMorning',
                null,
                [
                    'label' => 'Total matí',
                ]
            )
            ->add(
                'totalHoursAfternoon',
                null,
                [
                    'label' => 'Total hores tarda',
                ]
            )
            ->add(
                'priceHourAfternoon',
                null,
                [
                    'label' => 'Preu hora tarda',
                ]
            )
            ->add(
                'amountAfternoon',
                null,
                [
                    'label' => 'Total tarda',
                ]
            )
            ->add(
                'totalHoursNight',
                null,
                [
                    'label' => 'Total hores nit',
                ]
            )
            ->add(
                'priceHourNight',
                null,
                [
                    'label' => 'Preu hora nit',
                ]
            )
            ->add(
                'amountNight',
                null,
                [
                    'label' => 'Total nit',
                ]
            )
            ->add(
                'totalHoursEarlyMorning',
                null,
                [
                    'label' => 'Total hores matinada',
                ]
            )
            ->add(
                'priceHourEarlyMorning',
                null,
                [
                    'label' => 'Preu hora matinada',
                ]
            )
            ->add(
                'amountEarlyMorning',
                null,
                [
                    'label' => 'Total matinada',
                ]
            )
            ->add(
                'totalHoursDisplacement',
                null,
                [
                    'label' => 'Total hores desplaçament',
                ]
            )
            ->add(
                'priceHourDisplacement',
                null,
                [
                    'label' => 'Preu hora desplaçament',
                ]
            )
            ->add(
                'amountDisplacement',
                null,
                [
                    'label' => 'Total desplaçament',
                ]
            )
            ->add(
                'ivaType',
                null,
                [
                    'label' => 'Tipus IVA',
                ]
            )
            ->add(
                'retentionType',
                null,
                [
                    'label' => 'Tipus retenció',
                ]
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
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.saleRequest', 's')
                ->andWhere('s.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'saleRequest',
                null,
                [
                    'label' => 'Petició',
                ]
            )
            ->add(
                'saleDeliveryNote',
                null,
                [
                    'label' => 'Albarà',
                ]
            )
            ->add(
                'reference',
                null,
                [
                    'label' => 'Referència',
                ]
            )
            ->add(
                'ivaType',
                null,
                [
                    'label' => 'Tipus IVA',
                ]
            )
            ->add(
                'retentionType',
                null,
                [
                    'label' => 'Tipus retenció',
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
                    'label' => 'Accions',
                ]
            )
        ;
    }

    /**
     * @param SaleRequestHasDeliveryNote $object
     */
    public function prePersist($object)
    {
        $this->commonPreEvents($object);
    }

    /**
     * @param SaleRequestHasDeliveryNote $object
     */
    public function preUpdate($object)
    {
        $this->commonPreEvents($object);
    }

    /**
     * @param SaleRequestHasDeliveryNote $object
     */
    private function commonPreEvents($object)
    {
        $object->setAmountMorning($object->getTotalHoursMorning() * $object->getPriceHourMorning());
        $object->setAmountAfternoon($object->getTotalHoursAfternoon() * $object->getPriceHourAfternoon());
        $object->setAmountNight($object->getTotalHoursNight() * $object->getPriceHourNight());
        $object->setAmountEarlyMorning($object->getTotalHoursEarlyMorning() * $object->getPriceHourEarlyMorning());
        $object->setAmountDisplacement($object->getTotalHoursDisplacement() * $object->getPriceHourDisplacement());
    }
}
