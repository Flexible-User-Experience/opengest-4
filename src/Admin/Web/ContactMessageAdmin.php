<?php

namespace App\Admin\Web;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
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
    protected $translationDomain = 'admin';

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
    protected $datagridValues = array(
        '_sort_by' => 'createdAt',
        '_sort_order' => 'desc',
    );

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ->remove('delete')
            ->remove('batch')
            ->add('answer', $this->getRouterIdParameter().'/answer')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'checked',
                null,
                array(
                    'label' => 'admin.label.checked',
                )
            )
            ->add(
                'createdAt',
                DateFilter::class,
                array(
                    'label' => 'admin.label.date',
                    'field_type' => DatePickerType::class,
                ),
                null,
                array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'admin.label.email',
                )
            )
            ->add(
                'message',
                null,
                array(
                    'label' => 'admin.label.message',
                )
            )
            ->add(
                'answer',
                null,
                array(
                    'label' => 'admin.label.answer',
                )
            )
            ->add(
                'answered',
                null,
                array(
                    'label' => 'admin.label.answered',
                )
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add(
                'checked',
                null,
                array(
                    'label' => 'admin.label.checked',
                )
            )
            ->add(
                'createdAt',
                'date',
                array(
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y H:i',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'admin.label.email',
                )
            )
            ->add(
                'message',
                TextareaType::class,
                array(
                    'label' => 'admin.label.message',
                )
            )
            ->add(
                'answered',
                null,
                array(
                    'label' => 'admin.label.answered',
                )
            )
        ;
        if ($this->getSubject()->getAnswered()) {
            $showMapper
                ->add(
                    'answer',
                    'textarea',
                    array(
                        'label' => 'admin.label.answer',
                    )
                )
            ;
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'checked',
                null,
                array(
                    'label' => 'admin.label.checked',
                )
            )
            ->add(
                'createdAt',
                'date',
                array(
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'admin.label.email',
                )
            )
            ->add(
                'answered',
                null,
                array(
                    'label' => 'admin.label.answered',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array(
                            'template' => 'admin/buttons/list__action_show_button.html.twig',
                        ),
                        'answer' => array(
                            'template' => 'admin/cells/list__action_answer.html.twig',
                        ),
                    ),
                    'label' => 'admin.actions',
                )
            )
        ;
    }
}
