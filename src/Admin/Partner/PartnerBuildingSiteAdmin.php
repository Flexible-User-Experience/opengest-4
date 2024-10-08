<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;

/**
 * Class PartnerBuildingSiteAdmin.
 *
 * @category Admin
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class PartnerBuildingSiteAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tercers obres';

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
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'tercers/obres';
    }

    protected function configureFormFields(FormMapper $formMapper): void
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
                'number',
                null,
                [
                    'label' => 'admin.label.number',
                    'required' => false,
                ]
            )
            ->add(
                'address',
                null,
                [
                    'label' => 'admin.label.address',
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
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'partner',
                ModelFilter::class,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                            'property' => 'name',
                            'callback' => $this->partnerModelAutocompleteCallback(),
                        ],
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
                'number',
                null,
                [
                    'label' => 'admin.label.number',
                ]
            )
            ->add(
                'address',
                null,
                [
                    'label' => 'admin.label.address',
                ]
            )
            ->add(
                'phone',
                null,
                [
                    'label' => 'admin.label.phone',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.partner', 'p')
            ->orderBy('p.name', 'ASC')
        ;
        $queryBuilder->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'asc');
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere('p.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'partner',
                null,
                [
                    'label' => 'Tercer',
                    'admin_code' => 'app.admin.partner',
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
                'number',
                null,
                [
                    'label' => 'Número',
                    'editable' => true,
                ]
            )
            ->add(
                'address',
                null,
                [
                    'label' => 'Adreça',
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
