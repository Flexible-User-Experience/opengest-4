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
    protected $datagridValues = array(
        '_sort_by' => 'begin',
        '_sort_order' => 'desc',
    );

    /**
     * Methods.
     */

    /**
     * @param FormMapper $formMapper
     *
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'partner',
                ModelAutocompleteType::class,
                array(
                    'property_path' => 'name',
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
                'begin',
                DatePickerType::class,
                array(
                    'label' => 'Data inici',
                    'required' => true,
                    'format' => 'd/M/y',
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                )
            )
            ->add(
                'end',
                DatePickerType::class,
                array(
                    'label' => 'Data fi',
                    'format' => 'd/M/y',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
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
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $datagridMapper
                ->add(
                    'partner.enterprise',
                    null,
                    array(
                        'label' => 'Empresa',
                    )
                )
            ;
        }
        $datagridMapper
            ->add(
                'partner',
                'doctrine_orm_model_autocomplete',
                array(
                    'label' => 'Partner',
                ),
                null,
                array(
                    'property_path' => 'name',
                )
            )
            ->add(
                'begin',
                'doctrine_orm_date',
                array(
                    'label' => 'Data inici',
                    'field_type' => DatePickerType::class,
                )
            )
            ->add(
                'end',
                'doctrine_orm_date',
                array(
                    'label' => 'Data fi',
                    'field_type' => DatePickerType::class,
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

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $listMapper
                ->add(
                    'partner.enterprise',
                    null,
                    array(
                        'label' => 'Empresa',
                    )
                )
            ;
        }
        $listMapper
            ->add(
                'partner',
                null,
                array(
                    'label' => 'Tercer',
                    'editable' => false,
                )
            )
            ->add(
                'begin',
                null,
                array(
                    'label' => 'Data inici',
                    'format' => 'd/m/Y',
                    'editable' => true,
                )
            )
            ->add(
                'end',
                null,
                array(
                    'label' => 'Data fi',
                    'format' => 'd/m/Y',
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
