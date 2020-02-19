<?php

namespace App\Admin\Setting;

use App\Entity\Enterprise\Enterprise;
use App\Enum\UserRolesEnum;
use Sonata\UserBundle\Admin\Model\UserAdmin as ParentUserAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class UserAdmin.
 *
 * @category Admin
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class UserAdmin extends ParentUserAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    protected $classnameLabel = 'Usuari';
    protected $baseRoutePattern = 'configuracio/usuari';
    protected $datagridValues = array(
        '_sort_by' => 'username',
        '_sort_order' => 'asc',
    );

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param $userManager
     */
    public function __construct($code, $class, $baseControllerName, $userManager)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->userManager = $userManager;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('batch')
            ->remove('export')
            ->remove('show')
            ->add('profile', 'profile')
        ;
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /* @var object $formMapper */
        $formMapper
            ->with('General', array(
                'class' => 'col-md-6',
                'box_class' => 'box box-success',
                )
            )
            ->add(
                'firstname',
                null,
                array(
                    'label' => 'Nom',
                    'required' => false,
                )
            )
            ->add(
                'lastname',
                null,
                array(
                    'label' => 'Cognoms',
                    'required' => false,
                )
            )
            ->add(
                'username',
                null,
                array(
                    'label' => 'Nom usuari',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Correu electrònic',
                )
            )
            ->add(
                'plainPassword',
                'text',
                array(
                    'label' => 'Contrasenya',
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                )
            )
            ->end()
            ->with('Controls', array(
                'class' => 'col-md-6',
                'box_class' => 'box box-success',
                )
            )
            ->add(
                'enabled',
                'checkbox',
                array(
                    'label' => 'Actiu',
                    'required' => false,
                )
            )
            ->add(
                'defaultEnterprise',
                EntityType::class,
                array(
                    'class' => Enterprise::class,
                    'label' => 'Enpresa principal',
                    'required' => true,
                )
            )
            ->add(
                'roles',
                'choice',
                array(
                    'label' => 'Rols',
                    'choices' => UserRolesEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => true,
                )
            )
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add(
                'username',
                null,
                array(
                    'label' => 'Nom usuari',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Correu electrònic',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add(
                'username',
                null,
                array(
                    'label' => 'Nom usuari',
                    'editable' => true,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Correu electrònic',
                    'editable' => true,
                )
            )
            ->add(
                'defaultEnterprise',
                null,
                array(
                    'label' => 'Empresa principal',
                    'editable' => true,
                )
            )
            ->add(
                'roles',
                null,
                array(
                    'label' => 'Rols',
                    'template' => '::Admin/Cells/list__cell_user_roles.html.twig',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'label' => 'Accions',
                    'actions' => array(
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
                    ),
                )
            )
        ;
    }
}
