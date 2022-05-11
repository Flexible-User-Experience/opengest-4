<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleRequest;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class SaleRequestDocumentAdmin.
 *
 * @category Admin
 *
 * @author  Jordi Sort <jordi.sort@mirmit.com>
 */
class SaleRequestDocumentAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Documentos de petición';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'ventas/documentos_de_peticion';
    /**
     * Methods.
     */

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('downloadDocument', $this->getRouterIdParameter().'/documento')
            ->remove('delete')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
        ;
        if ($this->getCode() === $this->getRootCode()) {
            $formMapper
                ->add(
                    'saleRequest',
                    EntityType::class,
                    [
                        'label' => 'admin.label.sale_request',
                        'required' => true,
                        'class' => SaleRequest::class,
                        'placeholder' => '--- seleccione una opción ---',
                    ]
                )
            ;
        }
        $formMapper
            ->add(
                'documentFile',
                FileType::class,
                [
                    'label' => 'admin.with.document',
                    'help' => $this->getDocumentHelper('admin_app_sale_salerequestdocument_downloadDocument', 'document'),
                    'help_html' => true,
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
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'document',
                null,
                [
                    'label' => 'admin.with.document',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'document',
                null,
                [
                    'label' => 'admin.with.document',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'Acciones',
                ]
            )
        ;
    }
}
