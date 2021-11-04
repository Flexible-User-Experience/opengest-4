<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class VehicleAdmin.
 *
 * @category Admin
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Vehicles';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehicles/vehicle';

    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'vehicleRegistrationNumber',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle_registration_number',
                    'required' => true,
                ]
            )
            ->add(
                'chassisBrand',
                TextType::class,
                [
                    'label' => 'admin.label.chassis_brand',
                    'required' => true,
                ]
            )
            ->add(
                'chassisNumber',
                TextType::class,
                [
                    'label' => 'admin.label.chassis_number',
                ]
            )
            ->add(
                'vehicleBrand',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle_brand',
                ]
            )
            ->add(
                'vehicleModel',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle_model',
                ]
            )
            ->add(
                'serialNumber',
                TextType::class,
                [
                    'label' => 'admin.label.serial_number',
                ]
            )
            ->end()
            ->with('Recursos', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'mainImageFile',
                FileType::class,
                [
                    'label' => 'Imatge',
                    'help' => $this->getMainImageHelperFormMapperWithThumbnail(),
                    'required' => true,
                ]
            )
            ->add(
                'attatchmentPDFFile',
                FileType::class,
                [
                    'label' => 'Document',
                    'help' => $this->getDownloadPdfButton(),
                    'required' => false,
                ]
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'link',
                UrlType::class,
                [
                    'label' => 'admin.label.manufacturer_webpage',
                    'required' => false,
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'Actiu',
                    'required' => false,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'vehicleRegistrationNumber',
                null,
                [
                    'label' => 'admin.label.vehicle_registration_number',
                    'required' => true,
                ]
            )
            ->add(
                'chassisBrand',
                null,
                [
                    'label' => 'admin.label.chassis_brand',
                    'required' => true,
                ]
            )
            ->add(
                'chassisNumber',
                null,
                [
                    'label' => 'admin.label.chassis_number',
                ]
            )
            ->add(
                'vehicleBrand',
                null,
                [
                    'label' => 'admin.label.vehicle_brand',
                ]
            )
            ->add(
                'vehicleModel',
                null,
                [
                    'label' => 'admin.label.vehicle_model',
                ]
            )
            ->add(
                'serialNumber',
                null,
                [
                    'label' => 'admin.label.serial_number',
                ]
            )
            ->add(
                'link',
                null,
                [
                    'label' => 'Pàgina web fabricant',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
                ]
            );
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
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'mainImage',
                null,
                [
                    'label' => 'Imatge',
                    'template' => 'admin/cells/list__cell_main_image_field.html.twig',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'vehicleRegistrationNumber',
                null,
                [
                    'label' => 'admin.label.vehicle_registration_number',
                    'required' => true,
                ]
            )
            ->add(
                'chassisBrand',
                null,
                [
                    'label' => 'admin.label.chassis_brand',
                    'required' => true,
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
                    'editable' => true,
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
            );
    }

    /**
     * @param Vehicle $object
     */
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
