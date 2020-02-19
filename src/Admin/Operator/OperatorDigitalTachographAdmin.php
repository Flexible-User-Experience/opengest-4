<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class OperatorDigitalTachographAdmin.
 *
 * @category Admin
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 */
class OperatorDigitalTachographAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tacògrafs';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/tacograf';

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
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('delete')
            ->add('download', $this->getRouterIdParameter().'/download')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Arxiu', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'operator',
                EntityType::class,
                array(
                    'label' => 'Operari',
                    'required' => true,
                    'class' => Operator::class,
                    'choice_label' => 'fullName',
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'uploadedFile',
                FileType::class,
                array(
                    'label' => 'Arxiu tacògraf',
                    'help' => $this->getDownloadDigitalTachographButton(),
                    'required' => true,
                    'disabled' => $this->id($this->getSubject()) ? true : false,
                )
            )
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
                null,
                array(
                    'label' => 'Operari',
                )
            )
            ->add(
                'createdAt',
                'doctrine_orm_date',
                array(
                    'label' => 'Data creació',
                    'field_type' => DatePickerType::class,
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
            ->join($queryBuilder->getRootAliases()[0].'.operator', 'op')
            ->andWhere('op.enterprise = :enterprise')
            ->andWhere('op.enabled = :enabled')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->setParameter('enabled', true)
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
                'createdAt',
                'date',
                array(
                    'label' => 'Data',
                    'format' => 'd/m/Y',
                    'editable' => false,
                )
            )
            ->add(
                'operator.profilePhotoImage',
                null,
                array(
                    'label' => 'Imatge',
                    'template' => 'admin/cells/list__cell_operator_profile_image_field.html.twig',
                )
            )
            ->add(
                'operator',
                null,
                array(
                    'label' => 'Operari',
                    'editable' => false,
                    'associated_property' => 'fullName',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'surname1'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'operator')),
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'download' => array('template' => 'admin/buttons/list__action_download_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
            )

        ;
    }
}
