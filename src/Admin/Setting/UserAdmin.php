<?php

namespace App\Admin\Setting;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
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
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class UserAdmin extends AbstractBaseAdmin
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
    protected $datagridValues = [
        '_sort_by' => 'username',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
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
                    'label' => 'Nom',
                    'required' => false,
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => 'Cognoms',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Nom usuari',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Correu electrònic',
                ]
            )
            ->add(
                'plainPassword',
                TextType::class,
                [
                    'label' => 'Contrasenya',
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
                    'label' => 'Actiu',
                    'required' => false,
                ]
            )
            ->add(
                'defaultEnterprise',
                EntityType::class,
                [
                    'class' => Enterprise::class,
                    'label' => 'Enpresa principal',
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
                        'label' => 'Rols',
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
                    'label' => 'Nom usuari',
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'Correu electrònic',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
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
                    'label' => 'Nom usuari',
                    'editable' => true,
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'Correu electrònic',
                    'editable' => true,
                ]
            )
            ->add(
                'defaultEnterprise',
                null,
                [
                    'label' => 'Empresa principal',
                    'editable' => true,
                ]
            )
            ->add(
                'roles',
                null,
                [
                    'label' => 'Rols',
                    'template' => 'admin/cells/list__cell_user_roles.html.twig',
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
            $pass = $this->passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($pass);
            $user->setPlainPassword(null);
        }
    }
}
