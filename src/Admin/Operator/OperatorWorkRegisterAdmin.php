<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Sale\SaleDeliveryNote;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
    protected $classnameLabel = 'Partes de trabajo';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/partes-trabajo';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'date',
        '_sort_order' => 'desc',
    );

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'operator',
                EntityType::class,
                array(
                    'label' => 'admin.label.operator',
                    'required' => true,
                    'class' => Operator::class,
                    'choice_label' => 'fullName',
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'saleDeliveryNote',
                EntityType::class,
                array(
                    'label' => 'admin.with.delivery_note',
                    'required' => false,
                    'class' => SaleDeliveryNote::class,
                    'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getFilteredByEnterpriseSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'admin.label.date',
                    'required' => true,
                )
            )

            ->add(
                'start',
                TimeType::class,
                array(
                    'label' => 'admin.label.start',
                    'required' => true,
                )
            )
            ->add(
                'finish',
                TimeType::class,
                array(
                    'label' => 'admin.label.finish',
                    'required' => true,
                )
            )
            ->add(
                'units',
                null,
                array(
                    'label' => 'admin.label.units',
                    'required' => false,
                )
            )
            ->add(
                'priceUnit',
                null,
                array(
                    'label' => 'admin.label.price_unit',
                    'required' => true,
                )
            )
            ->add(
                'amount',
                null,
                array(
                    'label' => 'Total',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
                    'required' => false,
                )
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'Actiu',
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
                'operator',
                ModelAutocompleteFilter::class,
                array(
                    'label' => 'admin.label.operator',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'operator',
                null,
                array(
                    'label' => 'admin.label.operator',
                )
            )
            ->add(
                'saleDeliveryNote',
                null,
                array(
                    'label' => 'admin.with.sale_delivery_note',
                )
            )
            ->add(
                'date',
                null,
                array(
                    'label' => 'admin.label.date',
                    'format' => 'd/m/y',
                )
            )
            ->add(
                'start',
                null,
                array(
                    'label' => 'admin.label.start',
                    'editable' => true,
                )
            )
            ->add(
                'finish',
                null,
                array(
                    'label' => 'admin.label.finish',
                    'editable' => true,
                )
            )
            ->add(
                'units',
                null,
                array(
                    'label' => 'admin.label.units',
                )
            )
            ->add(
                'priceUnit',
                null,
                array(
                    'label' => 'Preu unitat',
                )
            )
            ->add(
                'amount',
                null,
                array(
                    'label' => 'Total',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Observacions',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
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
                    ),
                    'label' => 'Accions',
                )
            )
        ;
    }
}
