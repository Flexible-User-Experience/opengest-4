<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Route\RouteCollection;
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
    protected $datagridValues = array(
        '_sort_by' => 'createdAt',
        '_sort_order' => 'desc',
    );

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('delete')
            ->add('download', $this->getRouterIdParameter().'/download')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Arxiu', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'vehicle',
                EntityType::class,
                array(
                    'label' => 'Vehicle',
                    'required' => true,
                    'class' => Vehicle::class,
                    'choice_label' => 'name',
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'uploadedFile',
                FileType::class,
                array(
                    'label' => 'Arxiu tacògraf',
                    'help' => $this->getDownloadDigitalTachographButton(),
                    'required' => true,
                    'disabled' => $this->id($this->getSubject()) ? true : false,
                )
            )
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'vehicle',
                null,
                array(
                    'label' => 'Vehicle',
                )
            )
            ->add(
                'createdAt',
                DateFilter::class,
                array(
                    'label' => 'Data creació',
                    'field_type' => DatePickerType::class,
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
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.vehicle', 'v')
                ->andWhere('v.enterprise = :enterprise')
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
        $listMapper
            ->add(
                'createdAt',
                'date',
                array(
                    'label' => 'Data',
                    'format' => 'd/m/Y',
                    'editable' => false,
                )
            )
            ->add(
                'mainImage',
                null,
                array(
                    'label' => 'Imatge',
                    'template' => 'admin/cells/list__cell_tachograph_vehicle_image_field.html.twig',
                )
            )
            ->add(
                'Vehicle',
                null,
                array(
                    'label' => 'Vehicle',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'download' => array('template' => 'admin/buttons/list__action_download_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
            )
        ;
    }
}
