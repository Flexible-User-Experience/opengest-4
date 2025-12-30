<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Setting\User;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class EnterpriseAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Wils Iglesias <wiglesias83@gmail.com>
 */
class EnterpriseAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Empresa';

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
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'empreses/empresa';
    }

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('delete')
            ->remove('create')
            ->add('change', $this->getRouterIdParameter().'/change-user-default-enterprise')
            ->add('downloadTc1receipt', $this->getRouterIdParameter().'/tc1')
            ->add('downloadTc2receipt', $this->getRouterIdParameter().'/tc2')
            ->add('downloadSsRegistration', $this->getRouterIdParameter().'/registro-ss')
            ->add('downloadSsPaymentCertificate', $this->getRouterIdParameter().'/certificado-pago-ss')
            ->add('downloadRc1Insurance', $this->getRouterIdParameter().'/seguro-rc1')
            ->add('downloadRc2Insurance', $this->getRouterIdParameter().'/seguro-rc2')
            ->add('downloadRcReceipt', $this->getRouterIdParameter().'/recibo-rc')
            ->add('downloadPreventionServiceContract', $this->getRouterIdParameter().'/contracto-servicio-prevencion')
            ->add('downloadPreventionServiceInvoice', $this->getRouterIdParameter().'/factura-servicio-prevencion')
            ->add('downloadPreventionServiceReceipt', $this->getRouterIdParameter().'/recibo-servicio-prevencion')
            ->add('downloadOccupationalAccidentsInsurance', $this->getRouterIdParameter().'/seguro-accidentes-laborales')
            ->add('downloadOccupationalReceipt', $this->getRouterIdParameter().'/recibo-seguro-accidentes-laborales')
            ->add('downloadLaborRiskAssessment', $this->getRouterIdParameter().'/evaluacion-riesgos')
            ->add('downloadSecurityPlan', $this->getRouterIdParameter().'/plan-seguridad')
            ->add('downloadReaCertificate', $this->getRouterIdParameter().'/certificado-rea')
            ->add('downloadOilCertificate', $this->getRouterIdParameter().'/certificado-recogida-aceite')
            ->add('downloadGencatPaymentCertificate', $this->getRouterIdParameter().'/certificado-pago-gencat')
            ->add('downloadDeedsOfPowers', $this->getRouterIdParameter().'/escritura-poderes')
            ->add('downloadIaeRegistration', $this->getRouterIdParameter().'/registro-iae')
            ->add('downloadIaeReceipt', $this->getRouterIdParameter().'/recibo-iae')
            ->add('downloadMutualPartnership', $this->getRouterIdParameter().'/asociacion-mutua')
            ->add('downloadDeedOfIncorporation', $this->getRouterIdParameter().'/escritura-constitucion')
            ->add('downloadTaxIdentificationNumberCard', $this->getRouterIdParameter().'/tarjeta-nif')
            ->add('downloadLogoImage', $this->getRouterIdParameter().'/logo')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('Información')
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'logoFile',
                    FileType::class,
                    [
                        'label' => 'Logo',
                        'help' => $this->getLogoHelperFormMapperWithThumbnail(),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'taxIdentificationNumber',
                    null,
                    [
                        'label' => 'CIF',
                    ]
                )
                ->add(
                    'name',
                    null,
                    [
                        'label' => 'Nom',
                    ]
                )
                ->add(
                    'businessName',
                    null,
                    [
                        'label' => 'Nom fiscal',
                    ]
                )
                ->end()
                ->with('Contacte', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'address',
                    null,
                    [
                        'label' => 'Adreça',
                    ]
                )
                ->add(
                    'city',
                    null,
                    [
                        'label' => 'Ciutat',
                        'required' => true,
                    ]
                )
                ->add(
                    'email',
                    null,
                    [
                        'label' => 'Email',
                    ]
                )
                ->add(
                    'www',
                    null,
                    [
                        'label' => 'Web corporativa',
                    ]
                )
                ->add(
                    'phone1',
                    null,
                    [
                        'label' => 'Telèfon 1',
                    ]
                )
                ->add(
                    'phone2',
                    null,
                    [
                        'label' => 'Telèfon 2',
                    ]
                )
                ->add(
                    'phone3',
                    null,
                    [
                        'label' => 'Telèfon 3',
                    ]
                )
                ->end()
                ->with('Controls', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'users',
                    EntityType::class,
                    [
                        'label' => 'Usuaris',
                        'required' => false,
                        'class' => User::class,
                        'choice_label' => 'fullname',
                        'multiple' => true,
                        'query_builder' => $this->rm->getUserRepository()->getEnabledSortedByNameQB(),
                        'by_reference' => false,
                    ]
                )
                ->add(
                    'enabled',
                    CheckboxType::class,
                    [
                        'label' => 'Actiu',
                        'required' => false,
                    ]
                )
                ->end()
            ->end()
            ->tab('Recursos')
                ->with('TC\'s', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'tc1ReceiptFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.tc1_receipt',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadTc1receipt', 'tc1Receipt'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'tc2ReceiptFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.tc2_receipt',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadTc2receipt', 'tc2Receipt'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('Seguretat Social', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'ssRegistrationFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.ss_registration',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadSsRegistration', 'ssRegistration'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'ssPaymentCertificateFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.ss_payment_certificate',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadSsPaymentCertificate', 'ssPaymentCertificate'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.enterprise.civil_responsibility', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'rc1InsuranceFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.rc1_insurance',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadRc1Insurance', 'rc1Insurance'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'rc2InsuranceFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.rc2_insurance',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadRc2Insurance', 'rc2Insurance'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'rcReceiptFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.rc_receipt',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadRcReceipt', 'rcReceipt'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.enterprise.prevention_labour_risks', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'preventionServiceContractFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.prevention_service_contract',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadPreventionServiceContract', 'preventionServiceContract'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'preventionServiceInvoiceFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.prevention_service_invoice',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadPreventionServiceInvoice', 'preventionServiceInvoice'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'preventionServiceReceiptFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.prevention_service_receipt',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadPreventionServiceReceipt', 'preventionServiceReceipt'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.enterprise.insurances', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'occupationalAccidentsInsuranceFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.occupational_accidents_insurance',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadOccupationalAccidentsInsurance', 'occupationalAccidentsInsurance'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'occupationalReceiptFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.occupational_receipt',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadOccupationalReceipt', 'occupationalReceipt'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'laborRiskAssessmentFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.labor_risk_assessment',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadLaborRiskAssessment', 'laborRiskAssessment'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'securityPlanFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.security_plan',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadSecurityPlan', 'securityPlan'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.enterprise.iae', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'iaeRegistrationFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.iae_registration',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadIaeRegistration', 'iaeRegistration'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'iaeReceiptFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.iae_receipt',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadIaeReceipt', 'iaeReceipt'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.enterprise.other_documents', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'deedOfIncorporationFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.deed_of_incorporation',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadDeedOfIncorporation', 'deedOfIncorporation'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'taxIdentificationNumberCardFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.tax_identification_number_card',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadTaxIdentificationNumberCard', 'taxIdentificationNumberCard'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'reaCertificateFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.rea_certificate',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadReaCertificate', 'reaCertificate'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'oilCertificateFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.oil_certificate',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadOilCertificate', 'oilCertificate'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'gencatPaymentCertificateFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.gencat_payment_certificate',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadGencatPaymentCertificate', 'gencatPaymentCertificate'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'deedsOfPowersFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.deeds_of_powers',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadDeedsOfPowers', 'deedsOfPowers'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'mutualPartnershipFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.enterprise.mutual_partnership',
                        'help' => $this->getDocumentHelper('admin_app_enterprise_enterprise_downloadMutualPartnership', 'mutualPartnership'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'taxIdentificationNumber',
                null,
                [
                    'label' => 'CIF',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'Email',
                ]
            )
            ->add(
                'city',
                null,
                [
                    'label' => 'Ciutat',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.users', 'u')
                ->andWhere('u.id = :id')
                ->setParameter('id', $this->getUserLogedId())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'logo',
                null,
                [
                    'label' => 'Logo',
                    'template' => 'admin/cells/list__cell_logo_image_field.html.twig',
                ]
            )
            ->add(
                'taxIdentificationNumber',
                null,
                [
                    'label' => 'CIF',
                    'editable' => true,
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
                'email',
                null,
                [
                    'label' => 'Email',
                    'editable' => true,
                ]
            )
            ->add(
                'city',
                null,
                [
                    'label' => 'Ciutat',
                    'editable' => true,
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
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
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
