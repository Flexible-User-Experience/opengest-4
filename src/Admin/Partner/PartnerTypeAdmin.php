<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class PartnerTypeAdmin.
 *
 * @category Admin
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class PartnerTypeAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tercers tipus';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'tercers/tipus';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                    'required' => true,
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                    'required' => true,
                ]
            )
            ->add(
                'account',
                null,
                [
                    'label' => 'Compte',
                    'required' => false,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
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
                'account',
                null,
                [
                    'label' => 'Compte',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.partner.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                    'editable' => true,
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                    'editable' => true,
                ]
            )
            ->add(
                'account',
                null,
                [
                    'label' => 'Compte',
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
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
