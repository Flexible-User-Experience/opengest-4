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
    protected $datagridValues = array(
        '_sort_by' => 'partner.name',
        '_sort_order' => 'asc',
    );

    /**
     * Methods.
     */

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'partner',
                ModelAutocompleteType::class,
                array(
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
                            ->setParameter('enterprise', $this->getUserLogedEnterprise())
                        ;
                        $datagrid->setValue($property, null, $value);
                    },
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                    'required' => true,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'Telèfon',
                    'required' => false,
                )
            )
            ->add(
                'fax',
                null,
                array(
                    'label' => 'Fax',
                    'required' => false,
                )
            )
            ->add(
                'notes',
                null,
                array(
                    'label' => 'Notes',
                    'required' => false,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ),
                )
            )
            ->end()
            ->with('Càrrec', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'care',
                null,
                array(
                    'label' => 'Càrrec',
                    'required' => false,
                )
            )
            ->add(
                'mobile',
                null,
                array(
                    'label' => 'Mòbil',
                    'required' => false,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Email',
                    'required' => false,
                )
            )
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'partner',
                'doctrine_orm_model_autocomplete',
                array(
                    'label' => 'Tercer',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                )
            )
            ->add(
                'care',
                null,
                array(
                    'label' => 'Càrrec',
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'Telèfon',
                )
            )
            ->add(
                'mobile',
                null,
                array(
                    'label' => 'Mòbil',
                )
            )
            ->add(
                'fax',
                null,
                array(
                    'label' => 'Fax',
                )
            )
            ->add(
                'notes',
                null,
                array(
                    'label' => 'Notes',
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

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'partner',
                null,
                array(
                    'label' => 'Tercer',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'partner')),
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                    'editable' => true,
                )
            )
            ->add(
                'care',
                null,
                array(
                    'label' => 'Càrrec',
                    'editable' => true,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'Telèfon',
                    'editable' => true,
                )
            )
            ->add(
                'mobile',
                null,
                array(
                    'label' => 'Mòbil',
                    'editable' => true,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Email',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
            )
        ;
    }
}
