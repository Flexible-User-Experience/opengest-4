<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class EnterpriseGroupBountyAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class EnterpriseGroupBountyAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Prima';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'group',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'empreses/grup-prima';
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Grupo', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'group',
                    null,
                    [
                        'label' => 'admin.label.group',
                        'required' => true,
                    ]
                )
            ->end()
            ->with('Horas', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'normalHour',
                null,
                [
                    'label' => 'admin.label.normal_hour',
                    'required' => false,
                ]
            )
            ->add(
                'extraNormalHour',
                null,
                [
                    'label' => 'admin.label.extra_normal_hour',
                    'required' => false,
                ]
            )
            ->add(
                'extraExtraHour',
                null,
                [
                    'label' => 'admin.label.extra_extra_hour',
                    'required' => false,
                ]
            )
            ->add(
                'holidayHour',
                null,
                [
                    'label' => 'admin.label.holiday_hour',
                    'required' => false,
                ]
            )
            ->add(
                'negativeHour',
                null,
                [
                    'label' => 'admin.label.negative_hour',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Otros', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'lunch',
                null,
                [
                    'label' => 'admin.label.lunch',
                    'required' => false,
                ]
            )
            ->add(
                'dinner',
                null,
                [
                    'label' => 'admin.label.dinner',
                    'required' => false,
                ]
            )
            ->add(
                'internationalLunch',
                null,
                [
                    'label' => 'admin.label.international_lunch',
                    'required' => false,
                ]
            )
            ->add(
                'internationalDinner',
                null,
                [
                    'label' => 'admin.label.international_dinner',
                    'required' => false,
                ]
            )
            ->add(
                'diet',
                null,
                [
                    'label' => 'admin.label.diet',
                    'required' => false,
                ]
            )
            ->add(
                'extraNight',
                null,
                [
                    'label' => 'admin.label.extra_night',
                    'required' => false,
                ]
            )
            ->add(
                'overNight',
                null,
                [
                    'label' => 'admin.label.over_night',
                    'required' => false,
                ]
            )
            ->add(
                'carOutput',
                null,
                [
                    'label' => 'admin.label.car_output',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Primas', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'transp',
                null,
                [
                    'label' => 'admin.label.bounty.transp',
                    'required' => false,
                ]
            )
            ->add(
                'cp40',
                null,
                [
                    'label' => 'admin.label.bounty.cp40',
                    'required' => false,
                ]
            )
            ->add(
                'cpPlus40',
                null,
                [
                    'label' => 'admin.label.bounty.cpPlus40',
                    'required' => false,
                ]
            )
            ->add(
                'crane40',
                null,
                [
                    'label' => 'admin.label.bounty.crane40',
                    'required' => false,
                ]
            )
            ->add(
                'crane50',
                null,
                [
                    'label' => 'admin.label.bounty.crane50',
                    'required' => false,
                ]
            )
            ->add(
                'crane60',
                null,
                [
                    'label' => 'admin.label.bounty.crane60',
                    'required' => false,
                ]
            )
            ->add(
                'crane80',
                null,
                [
                    'label' => 'admin.label.bounty.crane80',
                    'required' => false,
                ]
            )
            ->add(
                'crane100',
                null,
                [
                    'label' => 'admin.label.bounty.crane100',
                    'required' => false,
                ]
            )
            ->add(
                'crane120',
                null,
                [
                    'label' => 'admin.label.bounty.crane120',
                    'required' => false,
                ]
            )
            ->add(
                'crane200',
                null,
                [
                    'label' => 'admin.label.bounty.crane200',
                    'required' => false,
                ]
            )
            ->add(
                'crane250300',
                null,
                [
                    'label' => 'admin.label.bounty.crane250300',
                    'required' => false,
                ]
            )
            ->add(
                'platform40',
                null,
                [
                    'label' => 'admin.label.bounty.platform40',
                    'required' => false,
                ]
            )
            ->add(
                'platform50',
                null,
                [
                    'label' => 'admin.label.bounty.platform50',
                    'required' => false,
                ]
            )
            ->add(
                'platform60',
                null,
                [
                    'label' => 'admin.label.bounty.platform60',
                    'required' => false,
                ]
            )
            ->add(
                'platform70',
                null,
                [
                    'label' => 'admin.label.bounty.platform70',
                    'required' => false,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add(
                'group',
                null,
                [
                    'label' => 'Grup prima',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
//            ->add(
//                'enterprise',
//                null,
//                array(
//                    'label' => 'Empresa',
//                )
//            )
            ->add(
                'group',
                null,
                [
                    'label' => 'admin.label.group',
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
//                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    /**
     * @param EnterpriseGroupBounty $object
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
