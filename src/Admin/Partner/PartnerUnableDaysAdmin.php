<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\DatePickerType;

/**
 * Class PartnerUnableDaysAdmin.
 *
 * @category Admin
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class PartnerUnableDaysAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tercers dies inhàbils';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'tercers/dies-inhabils';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'begin',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */

    /**
     * @throws Exception
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
                'begin',
                DatePickerType::class,
                [
                    'label' => 'admin.label.begin',
                    'required' => true,
                    'format' => 'd/M',
                    'dp_default_date' => (new \DateTime())->format('d/m'),
                ]
            )
            ->add(
                'end',
                DatePickerType::class,
                [
                    'label' => 'admin.label.end',
                    'format' => 'd/M',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m'),
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $datagridMapper
                ->add(
                    'partner.enterprise',
                    null,
                    [
                        'label' => 'Empresa',
                    ]
                )
            ;
        }
        $datagridMapper
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'Partner',
                    'admin_code' => 'app.admin.partner',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'begin',
                DateFilter::class,
                [
                    'label' => 'Data inici',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'end',
                DateFilter::class,
                [
                    'label' => 'Data fi',
                    'field_type' => DatePickerType::class,
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
            ->orderBy($queryBuilder->getRootAliases()[0].'.begin', 'DESC')
            ->addOrderBy('p.name', 'ASC')
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
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $listMapper
                ->add(
                    'partner.enterprise',
                    null,
                    [
                        'label' => 'Empresa',
                    ]
                )
            ;
        }
        $listMapper
            ->add(
                'partner',
                null,
                [
                    'label' => 'Tercer',
                    'admin_code' => 'app.admin.partner',
                    'editable' => false,
                ]
            )
            ->add(
                'begin',
                null,
                [
                    'label' => 'Data inici',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                null,
                [
                    'label' => 'Data fi',
                    'format' => 'd/m/Y',
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
