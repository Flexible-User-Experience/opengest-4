<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseHolidays;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;

/**
 * Class EnterpriseHolidaysAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class EnterpriseHolidaysAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Dies festius';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'empreses/dies-festius';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'day',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Dies festius', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'day',
                DatePickerType::class,
                [
                    'label' => 'Dia festiu',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom festivitat',
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
                'day',
                DateFilter::class,
                [
                    'label' => 'Dia festiu',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom festivitat',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.day', 'DESC')
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'enterprise',
                null,
                [
                    'label' => 'Empresa',
                ]
            )
            ->add(
                'day',
                null,
                [
                    'label' => 'Dia festiu',
                    'format' => 'd/m/y',
                    'editable' => true,
                ]
            )->add(
                'name',
                null,
                [
                    'label' => 'Nom festivitat',
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

    /**
     * @param EnterpriseHolidays $object
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
