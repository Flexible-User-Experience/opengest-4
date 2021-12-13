<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class VehicleCheckingAdmin.
 *
 * @category Admin
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleCheckingAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Revisions';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehicles/revisio';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'end',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'vehicle',
                EntityType::class,
                [
                    'label' => 'Vehicle',
                    'required' => true,
                    'class' => Vehicle::class,
                    'choice_label' => 'name',
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'Tipus revisió',
                    'required' => true,
                    'query_builder' => $this->rm->getVehicleCheckingTypeRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'begin',
                DatePickerType::class,
                [
                    'label' => 'Data d\'expedició',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'end',
                DatePickerType::class,
                [
                    'label' => 'Data de caducitat',
                    'format' => 'd/M/y',
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
                'vehicle',
                null,
                [
                    'label' => 'Vehicle',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'Tipus revisó',
                ]
            )
            ->add(
                'begin',
                DateFilter::class,
                [
                    'label' => 'Data d\'expedició',
                    'field_type' => DatePickerType::class,
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ]
            )
            ->add(
                'end',
                DateFilter::class,
                [
                    'label' => 'Data caducitat',
                    'field_type' => DatePickerType::class,
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.vehicle', 'v')
            ->andWhere('v.enabled = :enabled')
            ->setParameter('enabled', true)
        ;
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere('v.enterprise = :enterprise')
                ->setParameter('enterprise', $this->ts->getToken()->getUser()->getDefaultEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'status',
                null,
                [
                    'label' => 'Estat',
                    'template' => 'admin/cells/list__cell_vehicle_checking_status.html.twig',
                ]
            )
            ->add(
                'begin',
                'date',
                [
                    'label' => 'Data d\'expedició',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                'date',
                [
                    'label' => 'Data caducitat',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'Vehicle',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'vehicle']],
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'Tipus revisió',
                    'editable' => true,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'type']],
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
