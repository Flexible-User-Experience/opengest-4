<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Sale\SaleDeliveryNote;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class OperatorWorkRegisterAdmin.
 *
 * @category Admin
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 */
class OperatorWorkRegisterAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Partes de trabajo líneas';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/partes-trabajo';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'date',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('createCustomWorkRegister', 'createCustomWorkRegister')
            ->add('customDelete', 'customDelete')
            ->add('getJsonOperatorWorkRegistersByDataAndOperatorId', 'getOperatorWorkRegisters');
//            ->remove('delete')
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->getRootCode() == $this->getCode()) {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'saleDeliveryNote',
                    EntityType::class,
                    [
                        'label' => 'admin.with.delivery_note',
                        'required' => false,
                        'class' => SaleDeliveryNote::class,
                        'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getFilteredByEnterpriseSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->add(
                    'start',
                    TimeType::class,
                    [
                        'label' => 'admin.label.start',
                        'required' => true,
                    ]
                )
                ->add(
                    'finish',
                    TimeType::class,
                    [
                        'label' => 'admin.label.finish',
                        'required' => true,
                    ]
                )
                ->add(
                    'units',
                    null,
                    [
                        'label' => 'admin.label.units',
                        'required' => false,
                    ]
                )
                ->add(
                    'priceUnit',
                    null,
                    [
                        'label' => 'admin.label.price_unit',
                        'required' => true,
                    ]
                )
                ->add(
                    'amount',
                    null,
                    [
                        'label' => 'Total',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'description',
                    null,
                    [
                        'label' => 'admin.label.description',
                        'required' => false,
                    ]
                )
                ->end()
                ->with('Controls', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'enabled',
                    CheckboxType::class,
                    [
                        'label' => 'Actiu',
                        'required' => false,
                    ]
                )
                ->end();
        } else {
            // If is embedded in another admin
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'saleDeliveryNote',
                    null,
                    [
                        'label' => 'admin.with.delivery_note',
                        'required' => true,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'description',
                    null,
                    [
                        'label' => 'admin.label.description',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'start',
                    DateTimeType::class,
                    [
                        'label' => 'admin.label.start',
                        'required' => true,
                        'disabled' => true,
                        'widget' => 'single_text',
                        'format' => 'HH:mm',
                    ]
                )
                ->add(
                    'finish',
                    DateTimeType::class,
                    [
                        'label' => 'admin.label.finish',
                        'required' => true,
                        'disabled' => true,
                        'widget' => 'single_text',
                        'format' => 'HH:mm',
                    ]
                )
                ->add(
                    'units',
                    null,
                    [
                        'label' => 'admin.label.units',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'priceUnit',
                    null,
                    [
                        'label' => 'admin.label.price_unit',
                        'required' => true,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'amount',
                    null,
                    [
                        'label' => 'Total',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->end()
            ;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'operatorWorkRegisterHeader',
                null,
                [
                    'label' => 'admin.with.operator_work_register_header',
                ]
            )
            ->add(
                'saleDeliveryNote',
                null,
                [
                    'label' => 'admin.with.delivery_note',
                ]
            )
            ->add(
                'start',
                null,
                [
                    'label' => 'admin.label.start',
                    'editable' => true,
                ]
            )
            ->add(
                'finish',
                null,
                [
                    'label' => 'admin.label.finish',
                    'editable' => true,
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'Preu unitat',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'Total',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Observacions',
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
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }

    /**
     * @param OperatorWorkRegister $object
     *
     * @throws NonUniqueResultException
     */
    public function prePersist($object)
    {
        $object->setAmount($object->getUnits() * $object->getPriceUnit());

        $this->em->flush();
    }
}
