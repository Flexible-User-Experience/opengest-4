<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class ActivityLineAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class ActivityLineAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Línies d\'activitat';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'empreses/linies-activitat';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'name',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Línia d\'activitat', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'name',
                    null,
                    [
                        'label' => 'Línia d\'activitat',
                        'required' => true,
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
                    'label' => 'Línia d\'activitat',
                ]
            )
        ;
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'ASC')
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'enterprise',
                null,
                [
                    'label' => 'Empresa',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Línia d\'activitat',
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

    /**
     * @param ActivityLine $object
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
