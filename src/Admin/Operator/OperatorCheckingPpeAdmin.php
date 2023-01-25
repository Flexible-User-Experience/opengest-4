<?php

namespace App\Admin\Operator;

use App\Enum\OperatorCheckingTypeCategoryEnum;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class OperatorCheckingPpeAdmin.
 *
 * @category Admin
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class OperatorCheckingPpeAdmin extends OperatorCheckingBaseAdmin
{
    protected $classnameLabel = 'Epis';

    protected $baseRoutePattern = 'operarios/epis';

    protected $baseRouteName = 'admin_app_operator_operatorchecking_ppe';

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.with.operator_checking_type_ppe',
                    'required' => true,
                    'query_builder' => $this->rm
                        ->getOperatorCheckingTypeRepository()
                        ->getEnabledByTypeSortedByNameQB(OperatorCheckingTypeCategoryEnum::PPE),
                ]
            )
            ->end()
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->andWhere('oct.category = :category')
            ->setParameter('category', OperatorCheckingTypeCategoryEnum::PPE)
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'status',
                null,
                [
                    'label' => 'admin.label.status',
                    'template' => 'admin/cells/list__cell_operator_checking_status.html.twig',
                    'mapped' => false,
                ]
            )
            ->add(
                'operator.profilePhotoImage',
                null,
                [
                    'label' => 'admin.label.image',
                    'template' => 'admin/cells/list__cell_operator_profile_image_field.html.twig',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                    'editable' => false,
                    'associated_property' => 'fullName',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'surname1'],
                    'sort_parent_association_mappings' => [['fieldName' => 'operator']],
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.with.operator_checking_type_ppe',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'type']],
                ]
            )
            ->add(
                'begin',
                'date',
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                'date',
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'd/m/Y',
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
                    'label' => 'Acciones',
                ]
            )
        ;
    }
}
