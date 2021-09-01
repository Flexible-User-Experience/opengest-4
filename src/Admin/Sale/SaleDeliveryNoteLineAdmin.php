<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleItem;
use App\Enum\ConstantsEnum;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleDeliveryNoteLineAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleDeliveryNoteLineAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Albarà línia';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/albara-linia';

    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Albarà línia', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'saleItem',
                EntityType::class,
                [
                    'class' => SaleItem::class,
                    'label' => 'admin.label.item',
                    'required' => true,
                    'placeholder' => '--selecciona una opción---'
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                    'required' => false,
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'Unitats',
                    'required' => false,
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'Preu unitat',
                    'required' => true,
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'admin.label.discount',
                    'required' => false,
                ]
            )
            ->add(
                'iva',
                null,
                [
                    'label' => 'admin.label.iva',
                    'required' => true,
                    'empty_data' => (string) ConstantsEnum::IVA,
                    'attr' => [
                        'placeholder' => ConstantsEnum::IVA,
                    ],
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'admin.label.irpf',
                    'required' => true,
                    'empty_data' => (string) ConstantsEnum::IRPF,
                    'attr' => [
                        'placeholder' => ConstantsEnum::IRPF,
                    ],
                ]
            )
            ->add(
                'deliveryNote',
                EntityType::class,
                [
                    'class' => SaleDeliveryNote::class,
                    'label' => false,
                    'required' => true,
                    'attr' => [
                        'hidden' => 'true',
                    ],
                ]
            )
            ->add(
                'total',
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'deliveryNote',
                null,
                [
                    'label' => 'Albarà',
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'Unitats',
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
                'total',
                null,
                [
                    'label' => 'Total',
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'Descompte',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                ]
            )
            ->add(
                'iva',
                null,
                [
                    'label' => 'IVA',
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'IRPF',
                ]
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
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.deliveryNote', 's')
                ->where('s.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'deliveryNote',
                null,
                [
                    'label' => 'Albarà',
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'Unitats',
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
                'total',
                null,
                [
                    'label' => 'Total',
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'Descompte',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                ]
            )
            ->add(
                'iva',
                null,
                [
                    'label' => 'IVA',
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'IRPF',
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
}
