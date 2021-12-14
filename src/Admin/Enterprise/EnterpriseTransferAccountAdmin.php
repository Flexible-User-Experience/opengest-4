<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class EnterpriseTransferAccountAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class EnterpriseTransferAccountAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Compte Bancari';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'empreses/compte-bancari';

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
            ->with('Nom', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                    'required' => true,
                ]
            )
            ->end()
            ->with('Compte Bancari', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'iban',
                null,
                [
                    'label' => 'IBAN',
                    'required' => false,
                ]
            )
            ->add(
                'swift',
                null,
                [
                    'label' => 'SWIFT',
                    'required' => false,
                ]
            )
            ->add(
                'bankCode',
                null,
                [
                    'label' => 'Codi Entitat',
                    'required' => false,
                ]
            )
            ->add(
                'officeNumber',
                null,
                [
                    'label' => 'Codi Oficina',
                    'required' => false,
                ]
            )
            ->add(
                'controlDigit',
                null,
                [
                    'label' => 'Digit de control',
                    'required' => false,
                ]
            )
            ->add(
                'accountNumber',
                null,
                [
                    'label' => 'Número de compte',
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
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
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
                    'label' => 'Nom',
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
     * @param EnterpriseTransferAccount $object
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
