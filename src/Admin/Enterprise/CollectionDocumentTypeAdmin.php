<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\CollectionDocumentType;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class CollectionDocumentTypeAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class CollectionDocumentTypeAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tipus document cobrament';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'empreses/tipus-document-cobrament';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'ASC',
    );

    /**
     * Methods.
     */

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Tipus document cobrament', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                    'required' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Descripció',
                    'required' => false,
                )
            )
            ->add(
                'sitReference',
                null,
                array(
                    'label' => 'Referència SIT',
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
                'name',
                null,
                array(
                    'label' => 'Línia d\'activitat',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Descripció',
                )
            )
            ->add(
                'sitReference',
                null,
                array(
                    'label' => 'Referència SIT',
                )
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
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'ASC')
        ;

        return $queryBuilder;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'enterprise',
                null,
                array(
                    'label' => 'Empresa',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                    'editable' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Descripció',
                    'editable' => true,
                )
            )
            ->add(
                'sitReference',
                null,
                array(
                    'label' => 'Referència SIT',
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
                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
            )
        ;
    }

    /**
     * @param CollectionDocumentType $object
     */
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
