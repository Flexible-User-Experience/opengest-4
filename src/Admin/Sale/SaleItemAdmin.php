<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use App\Enum\SaleItemTypeEnum;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SaleItemAdmin.
 *
 * @category    Admin
 */
class SaleItemAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Items';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/items';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'description',
        '_sort_order' => 'ASC',
    );

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('delete')
        ;
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
                )
            )
            ->add(
                'unitPrice',
                null,
                array(
                    'label' => 'admin.label.unit_price',
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => SaleItemTypeEnum::getEnumArray(),
                    'label' => 'admin.label.type',
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
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
                )
            )
            ->add(
                'unitPrice',
                null,
                array(
                    'label' => 'admin.label.unit_price',
                )
            )
            ->add(
                'type',
                null,
                array(
                    'label' => 'admin.label.type',
                ),
                ChoiceType::class,
                array(
                    'choices' => SaleItemTypeEnum::getEnumArray(),
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
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
                    'editable' => true,
                )
            )
            ->add(
                'unitPrice',
                null,
                array(
                    'label' => 'admin.label.unit_price',
                    'editable' => true,
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => SaleItemTypeEnum::getEnumArray(),
                    'label' => 'admin.label.type',
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                    ),
                    'label' => 'admin.actions',
                )
            )
        ;
    }
}
