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
     * @var string
     */
    protected $baseRoutePattern = 'empreses/grup-prima';

    /**
     * @var string
     */
    protected $translationDomain = 'admin';

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
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Grupo', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'group',
                    null,
                    [
                        'label' => 'admin.label.group',
                        'required' => true,
                    ]
                )
            ->end()
            ->with('Horas', $this->getFormMdSuccessBoxArray(4))
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
                'negativeHour',
                null,
                [
                    'label' => 'admin.label.negative_hour',
                    'required' => false,
                ]
            )
//            ->add(
//                'transferHour',
//                null,
//                array(
//                    'label' => 'Hora de transfer',
//                    'required' => false,
//                )
//            )
//            ->add(
//                'roadExtraHour',
//                null,
//                array(
//                    'label' => 'Ctra. extra',
//                    'required' => false,
//                )
//            )
//            ->add(
//                'awaitingHour',
//                null,
//                array(
//                    'label' => 'Espera',
//                    'required' => false,
//                )
//            )
            ->end()
            ->with('Otros', $this->getFormMdSuccessBoxArray(4))
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
                'roadNormalHour',
                null,
                [
                    'label' => 'admin.label.road_normal_hour',
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
//            ->add(
//                'truckOutput',
//                null,
//                array(
//                    'label' => 'Sortida camió',
//                    'required' => false,
//                )
//            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
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
