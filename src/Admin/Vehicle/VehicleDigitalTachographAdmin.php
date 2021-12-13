<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class VehicleDigitalTachographAdmin.
 *
 * @category Admin
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 */
class VehicleDigitalTachographAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tacògrafs';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehicles/tacograf';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'createdAt',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('delete')
            ->add('download', $this->getRouterIdParameter().'/download')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Arxiu', $this->getFormMdSuccessBoxArray(6))
            ;
        if ($this->getRootCode() == $this->getCode()) {
            $formMapper
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
                );
        } else {
            $formMapper
                ->add(
                    'vehicle',
                    EntityType::class,
                    [
                        'label' => 'Vehicle',
                        'required' => true,
                        'class' => Vehicle::class,
                        'choice_label' => 'name',
                        'attr' => [
                            'hidden' => true,
                        ],
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                );
        }
        $formMapper
            ->add(
                'createdAt',
                DatePickerType::class,
                [
                    'label' => 'Fecha importación',
                    'format' => 'dd/MM/yyyy',
                    'disabled' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'uploadedFile',
                FileType::class,
                [
                    'label' => 'Arxiu tacògraf',
                    'help' => $this->getDownloadDigitalTachographButton(),
                    'required' => true,
                    'disabled' => $this->id($this->getSubject()) ? true : false,
                ]
            )
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
                'createdAt',
                DateFilter::class,
                [
                    'label' => 'Data creació',
                    'field_type' => DatePickerType::class,
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.vehicle', 'v')
                ->andWhere('v.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
                ->orderBy($queryBuilder->getRootAliases()[0].'.created_at', 'DESC')
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'createdAt',
                'date',
                [
                    'label' => 'Data',
                    'format' => 'd/m/Y',
                    'editable' => false,
                ]
            )
            ->add(
                'mainImage',
                null,
                [
                    'label' => 'Imatge',
                    'template' => 'admin/cells/list__cell_main_image_field.html.twig',
                ]
            )
            ->add(
                'Vehicle',
                null,
                [
                    'label' => 'Vehicle',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'download' => ['template' => 'admin/buttons/list__action_download_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
