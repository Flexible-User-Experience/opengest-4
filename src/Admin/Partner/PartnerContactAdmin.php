<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;

/**
 * Class PartnerContactAdmin.
 *
 * @category Admin
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class PartnerContactAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tercers contacte';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'tercers/contacte';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'partner.name',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ;
        if ($this->getRootCode() == $this->getCode()) {
            $formMapper
                ->add(
                    'partner',
                    ModelAutocompleteType::class,
                    [
                        'property' => 'name',
                        'label' => 'Tercer',
                        'required' => true,
                        'callback' => function ($admin, $property, $value) {
                            /** @var Admin $admin */
                            $datagrid = $admin->getDatagrid();
                            /** @var QueryBuilder $queryBuilder */
                            $queryBuilder = $datagrid->getQuery();
                            $queryBuilder
                                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                                ->setParameter('enterprise', $this->getUserLogedEnterprise());
                            $datagrid->setValue($property, null, $value);
                        },
                    ],
                    [
                        'admin_code' => 'app.admin.partner',
                    ]
                );
        }
        $formMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                    'required' => true,
                ]
            )
            ->add(
                'care',
                null,
                [
                    'label' => 'admin.label.care',
                    'required' => false,
                ]
            )
            ->add(
                'phone',
                null,
                [
                    'label' => 'admin.label.phone',
                    'required' => false,
                ]
            )
            ->add(
                'mobile',
                null,
                [
                    'label' => 'admin.label.mobile',
                    'required' => false,
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                    'required' => false,
                ]
            )
            ->add(
                'fax',
                null,
                [
                    'label' => 'admin.label.fax',
                    'required' => false,
                ]
            )
            ->add(
                'notes',
                null,
                [
                    'label' => 'admin.label.notes',
                    'required' => false,
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 4,
                    ],
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'Tercer',
                    'admin_code' => 'partner_admin',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'care',
                null,
                [
                    'label' => 'Càrrec',
                ]
            )
            ->add(
                'phone',
                null,
                [
                    'label' => 'Telèfon',
                ]
            )
            ->add(
                'mobile',
                null,
                [
                    'label' => 'Mòbil',
                ]
            )
            ->add(
                'fax',
                null,
                [
                    'label' => 'Fax',
                ]
            )
            ->add(
                'notes',
                null,
                [
                    'label' => 'Notes',
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
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.partner', 'p')
            ->orderBy('p.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'ASC')
        ;
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere('p.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'partner',
                null,
                [
                    'label' => 'Tercer',
                    'admin_code' => 'partner_admin',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'partner']],
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                    'editable' => true,
                ]
            )
            ->add(
                'care',
                null,
                [
                    'label' => 'Càrrec',
                    'editable' => true,
                ]
            )
            ->add(
                'phone',
                null,
                [
                    'label' => 'Telèfon',
                    'editable' => true,
                ]
            )
            ->add(
                'mobile',
                null,
                [
                    'label' => 'Mòbil',
                    'editable' => true,
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'Email',
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
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
