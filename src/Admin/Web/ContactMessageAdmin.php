<?php

namespace App\Admin\Web;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class ContactMessageAdmin.
 *
 * @category    Admin
 */
class ContactMessageAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Missatge de contacte';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'web/missatge-contacte';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'createdAt',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ->remove('delete')
            ->remove('batch')
            ->add('answer', $this->getRouterIdParameter().'/answer')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'checked',
                null,
                [
                    'label' => 'admin.label.checked',
                ]
            )
            ->add(
                'createdAt',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.date',
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'Desde',
                            'format' => 'dd/MM/yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'Hasta',
                            'format' => 'dd/MM/yyyy',
                        ],
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
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                ]
            )
            ->add(
                'message',
                null,
                [
                    'label' => 'admin.label.message',
                ]
            )
            ->add(
                'answer',
                null,
                [
                    'label' => 'admin.label.answer',
                ]
            )
            ->add(
                'answered',
                null,
                [
                    'label' => 'admin.label.answered',
                ]
            )
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add(
                'checked',
                null,
                [
                    'label' => 'admin.label.checked',
                ]
            )
            ->add(
                'createdAt',
                'date',
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y H:i',
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
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'label' => 'admin.label.message',
                ]
            )
            ->add(
                'answered',
                null,
                [
                    'label' => 'admin.label.answered',
                ]
            )
        ;
        if ($this->getSubject()->getAnswered()) {
            $showMapper
                ->add(
                    'answer',
                    'textarea',
                    [
                        'label' => 'admin.label.answer',
                    ]
                )
            ;
        }
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'checked',
                null,
                [
                    'label' => 'admin.label.checked',
                ]
            )
            ->add(
                'createdAt',
                'date',
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y',
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
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                ]
            )
            ->add(
                'answered',
                null,
                [
                    'label' => 'admin.label.answered',
                    'editable' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => [
                            'template' => 'admin/buttons/list__action_show_button.html.twig',
                        ],
                        'answer' => [
                            'template' => 'admin/cells/list__action_answer.html.twig',
                        ],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
