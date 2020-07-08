<?php

namespace App\Admin\Setting;

use Sonata\UserBundle\Admin\Model\UserAdmin as ParentUserAdmin;
use App\Entity\Enterprise\Enterprise;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
     * @var string
     */
    protected $classnameLabel = 'Usuari';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'configuracio/usuari';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'username',
        '_sort_order' => 'asc',
    );

    /**
     * Methods.
     */

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
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', array(
                'class' => 'col-md-6',
                'box_class' => 'box box-success',
                )
            )
            ->add(
                'firstname',
                TextType::class,
                array(
                    'label' => 'Nom',
                    'required' => false,
                )
            )
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'Cognoms',
                    'required' => false,
                )
            )
            ->add(
                'username',
                TextType::class,
                array(
                    'label' => 'Nom usuari',
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'label' => 'Correu electrònic',
                )
            )
            ->add(
                'plainPassword',
                TextType::class,
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
                CheckboxType::class,
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
                ChoiceType::class,
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
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
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
    protected function configureListFields(ListMapper $listMapper): void
    {
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
                    'template' => 'admin/cells/list__cell_user_roles.html.twig',
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
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ),
                )
            )
        ;
    }
}
