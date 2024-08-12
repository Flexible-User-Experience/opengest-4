<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Partner\Partner;
use App\Entity\Setting\City;
use App\Entity\Setting\Province;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class PartnerDeliveryAddressAdmin.
 *
 * @category Admin
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com
 */
class PartnerDeliveryAddressAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Direcciones envio cliente';

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'terceros/direccion-envio';
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ;
        if ($this->getCode() === $this->getRootCode()) {
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
                )
            ;
        } else {
            $formMapper
                ->add(
                    'partner',
                    EntityType::class,
                    [
                        'class' => Partner::class,
                        'label' => false,
                        'required' => true,
                        'attr' => [
                            'hidden' => 'true',
                        ],
                    ],
                    [
                        'admin_code' => 'app.admin.partner',
                    ]
                )
            ;
        }
        $formMapper
            ->add(
                'address',
                null,
                [
                    'label' => 'admin.label.address',
                    'required' => true,
                ]
            )
            ->add(
                'city',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'admin.label.city',
                    'required' => true,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ;
        if ($this->getSubject()->getCity()) {
            $formMapper
                ->add(
                    'city.province',
                    EntityType::class,
                    [
                        'class' => Province::class,
                        'label' => 'admin.label.province',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
            ;
        }
        $formMapper
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
                    'label' => 'admin.label.third_party',
                    'admin_code' => 'app.admin.partner',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                            'property' => 'name',
                            'callback' => $this->partnerModelAutocompleteCallback(),
                        ],
                ]
            )
        ;
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
                'address',
                null,
                [
                    'label' => 'admin.label.address',
                    'editable' => true,
                ]
            )
            ->add(
                'city',
                null,
                [
                    'label' => 'admin.label.city',
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
