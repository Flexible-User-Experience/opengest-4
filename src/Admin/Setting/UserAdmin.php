<?php

namespace App\Admin\Setting;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
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
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class UserAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Usuari';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'username',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'configuracio/usuari';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('batch')
            ->remove('export')
            ->remove('show')
            ->add('profile', 'profile')
        ;
    }

    public function configureBatchActions(array $actions): array
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', [
                'class' => 'col-md-6',
                'box_class' => 'box box-success',
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => 'admin.label.name',
                    'required' => false,
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => 'admin.label.surname',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'admin.label.username',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'admin.label.email',
                ]
            )
            ->add(
                'plainPassword',
                TextType::class,
                [
                    'label' => 'admin.label.password',
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                ]
            )
            ->end()
            ->with('Controls', [
                'class' => 'col-md-6',
                'box_class' => 'box box-success',
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'enabled',
                    'required' => false,
                ]
            )
            ->add(
                'defaultEnterprise',
                EntityType::class,
                [
                    'class' => Enterprise::class,
                    'label' => 'admin.label.main_company',
                    'required' => true,
                ]
            )
            ;
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->add(
                    'roles',
                    ChoiceType::class,
                    [
                        'label' => 'admin.label.role',
                        'choices' => UserRolesEnum::getEnumArray(),
                        'multiple' => true,
                        'expanded' => true,
                    ]
                )
            ;
        }
        $formMapper
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add(
                'username',
                null,
                [
                    'label' => 'admin.label.username',
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'username',
                null,
                [
                    'label' => 'admin.label.username',
                    'editable' => true,
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                    'editable' => true,
                ]
            )
            ->add(
                'defaultEnterprise',
                null,
                [
                    'label' => 'admin.label.main_company',
                    'editable' => true,
                ]
            )
            ->add(
                'roles',
                null,
                [
                    'label' => 'admin.label.role',
                    'template' => 'admin/cells/list__cell_user_roles.html.twig',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                    'editable' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'Accions',
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                ]
            )
        ;
    }

    /**
     * @param User $object
     */
    public function prePersist($object): void
    {
        $this->managePassword($object);
    }

    public function preUpdate($object): void
    {
        $this->managePassword($object);
    }

    private function managePassword($user)
    {
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword) {
            $pass = $this->passwordEncoder->hashPassword($user, $plainPassword);
            $user->setPassword($pass);
            $user->setPlainPassword(null);
        }
    }
}
