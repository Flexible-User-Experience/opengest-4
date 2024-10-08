<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Sale\SaleDeliveryNote;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\DatePickerType;
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
    protected $classnameLabel = 'Partes de trabajo líneas';

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
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'operaris/partes-trabajo';
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'operatorWorkRegisterHeader.dateFormatted',
            'operatorWorkRegisterHeader.operator',
            'saleDeliveryNote',
            'saleDeliveryNote.vehicle',
            'start',
            'finish',
            'units',
            'priceUnit',
            'amount',
            'description',
        ];
    }

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('createCustomWorkRegister', 'createCustomWorkRegister')
            ->add('customDelete', 'customDelete')
            ->add('getJsonOperatorWorkRegistersByDataAndOperatorId', 'getOperatorWorkRegisters')
            ->add('customChangeDeliveryNote', 'custom-change-delivery-note')
            ->remove('create')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
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
                        'label' => 'admin.label.total',
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
                        'label' => 'admin.label.enabled_male',
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
                        'placeholder' => ' - ',
                        'required' => false,
                        'disabled' => true,
                        'attr' => [
                            'hidden' => 'true',
                        ],
                    ]
                )
                ->add(
                    'operatorWorkRegisterHeader.operator',
                    EntityType::class,
                    [
                        'class' => Operator::class,
                        'label' => 'admin.label.operator',
                        'required' => true,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'operatorWorkRegisterHeader.date',
                    DatePickerType::class,
                    [
                        'label' => 'admin.label.date',
                        'format' => 'dd/MM/yyyy',
                        'required' => true,
                        'dp_default_date' => (new \DateTime())->format('d/m/Y'),
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
                        'html5' => false,
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
                        'html5' => false,
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
                        'label' => 'admin.label.total',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->end()
            ;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
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
                    'label' => 'admin.label.price_unit',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.total',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.comments',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
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
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    /**
     * @param OperatorWorkRegister $object
     */
    public function prePersist($object): void
    {
        $object->setAmount($object->getUnits() * $object->getPriceUnit());
    }

    /**
     * @param OperatorWorkRegister $object
     */
    public function preUpdate($object): void
    {
        $object->setAmount($object->getUnits() * $object->getPriceUnit());
        if ($object->getStart()->getTimestamp() === $object->getFinish()->getTimestamp()) {
            $object->setStart(null);
            $object->setFinish(null);
        }
    }
}
