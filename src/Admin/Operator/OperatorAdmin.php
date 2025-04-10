<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use App\Entity\Operator\Operator;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Enum\OperatorTypeEnum;
use App\Enum\UserRolesEnum;
use App\Manager\DeliveryNoteManager;
use App\Manager\InvoiceManager;
use App\Manager\PayslipManager;
use App\Manager\RepositoriesManager;
use App\Manager\VehicleMaintenanceManager;
use App\Manager\YearChoicesManager;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

/**
 * Class OperatorAdmin.
 *
 * @author Wils Iglesias <wiglesias83@gmail.com>
 */
class OperatorAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Operadors';


    /**
     * Methods.
     */
    public function __construct(CacheManager $lis, YearChoicesManager $ycm, InvoiceManager $im, RepositoriesManager $rm, DeliveryNoteManager $dnm, VehicleMaintenanceManager $vmm, PayslipManager $payslipManager, EntityManagerInterface $em, FileService $fs, Environment $tws, TokenStorageInterface $ts, AuthorizationCheckerInterface $acs, UserPasswordHasherInterface $passwordEncoder,
                                public array $purchaseInvoiceLinesCostCenters = []
    )
    {
        parent::__construct($lis, $ycm, $im, $rm, $dnm, $vmm, $payslipManager, $em, $fs, $tws, $ts, $acs, $passwordEncoder);
    }

    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'operaris/operari';
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'surname1';
        $sortValues[DatagridInterface::PER_PAGE] = 50;
    }

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('downloadProfilePhotoImage', $this->getRouterIdParameter().'/profilePhoto')
            ->add('downloadTaxIdentificationNumberImage', $this->getRouterIdParameter().'/dni')
            ->add('downloadDrivingLicenseImage', $this->getRouterIdParameter().'/permiso-conducir')
            ->add('downloadCranesOperatorLicenseImage', $this->getRouterIdParameter().'/licencia-operador-gruas')
            ->add('downloadMedicalCheckImage', $this->getRouterIdParameter().'/revision-medica')
            ->add('downloadEpisImage', $this->getRouterIdParameter().'/epis')
            ->add('downloadTrainingDocumentImage', $this->getRouterIdParameter().'/formacion')
            ->add('downloadInformationImage', $this->getRouterIdParameter().'/informacion')
            ->add('downloadUseOfMachineryAuthorizationImage', $this->getRouterIdParameter().'/autorizacion-maquinaria')
            ->add('downloadDischargeSocialSecurityImage', $this->getRouterIdParameter().'/baja-seguridad-social')
            ->add('downloadEmploymentContractImage', $this->getRouterIdParameter().'/contrato-de-trabajo')
            ->add('generatePayslips', 'generate-payslips')
            ->add('generateDocumentation', 'generate-documentation')
            ->add('batch')
            ->remove('delete');
    }

    public function configureExportFields(): array
    {
        return [
            'profilePhotoImage',
            'taxIdentificationNumber',
            'name',
            'surname1',
            'surname2',
            'email',
            'address',
            'city',
            'enterpriseMobile',
            'ownPhone',
            'ownMobile',
            'hasCarDrivingLicense',
            'hasLorryDrivingLicense',
            'hasTowingDrivingLicense',
            'hasCraneDrivingLicense',
            'enterpriseGroupBounty',
            'brithDate',
            'registrationDate',
            'bancAccountNumber',
            'socialSecurityNumber',
            'shoeSize',
            'jerseytSize',
            'jacketSize',
            'tShirtSize',
            'pantSize',
            'workingDressSize',
            'enabled',
        ];
    }

    public function configureBatchActions(array $actions): array
    {
        if (
            $this->hasRoute('edit')
        ) {
            $actions['createPayslipFromOperators'] = [
                'label' => 'admin.action.generate_payslips_from_selected',
                'ask_confirmation' => false,
            ];
            $actions['downloadDocumentation'] = [
                'label' => 'admin.action.download_documentation',
                'ask_confirmation' => false,
            ];
        }

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('General')
                ->with('General', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'profilePhotoImageFile',
                        FileType::class,
                        [
                            'label' => 'profilePhotoImage',
                            'help' => $this->getProfileHelperFormMapperWithThumbnail(),
                            'help_html' => true,
                            'required' => false,
                        ]
                    )
                    ->add(
                        'taxIdentificationNumber',
                        null,
                        [
                            'label' => 'taxIdentificationNumber',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'name',
                        null,
                        [
                            'label' => 'name',
                        ]
                    )
                    ->add(
                        'surname1',
                        null,
                        [
                            'label' => 'surname1',
                        ]
                    )
                    ->add(
                        'surname2',
                        null,
                        [
                            'label' => 'surname2',
                        ]
                    )
                    ->add(
                        'type',
                        ChoiceType::class,
                        [
                            'choices' => OperatorTypeEnum::getEnumArray(),
                            'label' => 'admin.label.type',
                        ]
                    )
                ->end()
                ->with('Contacto', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'email',
                        null,
                        [
                            'label' => 'email',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'address',
                        null,
                        [
                            'label' => 'address',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'city',
                        null,
                        [
                            'label' => 'city',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'enterpriseMobile',
                        null,
                        [
                            'label' => 'enterpriseMobile',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'ownPhone',
                        null,
                        [
                            'label' => 'ownPhone',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'ownMobile',
                        null,
                        [
                            'label' => 'ownMobile',
                            'required' => false,
                        ]
                    )
                ->end()
            ->with('EPI\'s', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'shoeSize',
                null,
                [
                    'label' => 'shoeSize',
                    'required' => false,
                ]
            )
            ->add(
                'jerseytSize',
                null,
                [
                    'label' => 'jerseytSize',
                    'required' => false,
                ]
            )
            ->add(
                'jacketSize',
                null,
                [
                    'label' => 'jacketSize',
                    'required' => false,
                ]
            )
            ->add(
                'tShirtSize',
                null,
                [
                    'label' => 'tShirtSize',
                    'required' => false,
                ]
            )
            ->add(
                'pantSize',
                null,
                [
                    'label' => 'pantSize',
                    'required' => false,
                ]
            )
            ->add(
                'workingDressSize',
                null,
                [
                    'label' => 'workingDressSize',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Licencias', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'hasCarDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'hasCarDrivingLicense',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'hasLorryDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'hasLorryDrivingLicense',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'hasTowingDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'hasTowingDrivingLicense',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'hasCraneDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'hasCraneDrivingLicense',
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Controles', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'enterpriseGroupBounty',
                        EntityType::class,
                        [
                            'class' => EnterpriseGroupBounty::class,
                            'label' => 'enterpriseGroupBounty',
                            'placeholder' => '--- seleccione una opcion ---',
                            'required' => false,
                            'query_builder' => $this->rm->getEnterpriseGroupBountyRepository()->getEnabledSortedByNameQB(),
                        ]
                    )
                    ->add(
                        'brithDate',
                        DatePickerType::class,
                        [
                            'label' => 'brithDate',
                            'format' => 'd/M/y',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'registrationDate',
                        DatePickerType::class,
                        [
                            'label' => 'registrationDate',
                            'format' => 'd/M/y',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'bancAccountNumber',
                        null,
                        [
                            'label' => 'bancAccountNumber',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'socialSecurityNumber',
                        null,
                        [
                            'label' => 'socialSecurityNumber',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'enabled',
                        CheckboxType::class,
                        [
                            'label' => 'admin.label.enabled',
                            'required' => false,
                        ]
                    )
                ->end()
            ->end()
        ;
        if ($this->id($this->getSubject())) {
            $this->operatorAbsences = $this->rm->getOperatorAbsenceRepository()->getAbsencesFilteredByOperator($this->getSubject());
            $this->purchaseInvoiceLinesCostCenters = $this->em->getRepository(PurchaseInvoiceLine::class)->getCostCenters(operator: $this->getSubject());

            $formMapper
                ->tab('Documentación')
                ->with('admin.with.operator.tax_identification_number', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'taxIdentificationNumberImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.dni_nie',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadTaxIdentificationNumberImage', 'taxIdentificationNumberImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.social_security', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'dischargeSocialSecurityImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.discharge_social_security',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadDischargeSocialSecurityImage', 'dischargeSocialSecurityImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.employment_contract', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'employmentContractImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.contract',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadEmploymentContractImage', 'employmentContractImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.medical_report', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'medicalCheckImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.medical_check',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadMedicalCheckImage', 'medicalCheckImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.epis', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'episImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.epis',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadEpisImage', 'episImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.training_document', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'trainingDocumentImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.training_title',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadTrainingDocumentImage', 'trainingDocumentImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.information', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'informationImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.information',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadInformationImage', 'informationImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.operator.licences', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'drivingLicenseImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.driving_license',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadDrivingLicenseImage', 'drivingLicenseImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'useOfMachineryAuthorizationImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.use_of_machinery_authorization',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadUseOfMachineryAuthorizationImage', 'useOfMachineryAuthorizationImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->add(
                    'cranesOperatorLicenseImageFile',
                    FileType::class,
                    [
                        'label' => 'admin.with.operator.cranes_operator_license',
                        'help' => $this->getDocumentHelper('admin_app_operator_operator_downloadCranesOperatorLicenseImage', 'cranesOperatorLicenseImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.label.other_documents', $this->getFormMdSuccessBoxArray(7))
                ->add(
                    'documents',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Revisiones')
                ->with('Revisiones', $this->getFormMdSuccessBoxArray(8))
                ->add(
                    'operatorCheckings',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Epis')
                ->with('Epis', $this->getFormMdSuccessBoxArray(8))
                ->add(
                    'operatorCheckingPpes',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Formaciones')
                ->with('Formaciones', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'operatorCheckingTrainings',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                        'by_reference' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'admin_code' => 'app.admin.operator_checking_training',
                    ]
                )
                ->end()
                ->end()
                ->tab('Ausencias')
                ->with('Ausencias', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'operatorAbsences',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Tacógrafo')
                ->with('Tacógrafo', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'operatorDigitalTachographs',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Nóminas')
                ->with('Conceptos por defecto', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'payslipOperatorDefaultLines',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->with('Nóminas', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'payslips',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                        'btn_add' => false,
                        'disabled' => true,
                        'type_options' => [
                            'delete' => false,
                        ],
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Facturas de compra')
                ->with('Lineas de factura de compra', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'invoiceLines',
                    null,
                    [
                        'label' => 'admin.label.purchase_invoice_lines',
                        'mapped' => false,
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->end()
                ->end()
                ;
        }
        $asd = 1;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'taxIdentificationNumber',
                null,
                [
                    'label' => 'taxIdentificationNumber',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'name',
                ]
            )
            ->add(
                'surname1',
                null,
                [
                    'label' => 'surname1',
                ]
            )
            ->add(
                'surname2',
                null,
                [
                    'label' => 'surname2',
                ]
            )
            ->add(
                'enterpriseGroupBounty',
                null,
                [
                    'label' => 'enterpriseGroupBounty',
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'email',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                ]
            )
        ;
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        $filterValues['enabled'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_YES,
        ];
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'taxIdentificationNumber',
                null,
                [
                    'label' => 'taxIdentificationNumber',
                    'editable' => true,
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'name',
                    'editable' => true,
                ]
            )
            ->add(
                'surname1',
                null,
                [
                    'label' => 'surname1',
                    'editable' => true,
                ]
            )
            ->add(
                'surname2',
                null,
                [
                    'label' => 'surname2',
                    'editable' => true,
                ]
            )
            ->add(
                'enterpriseMobile',
                null,
                [
                    'label' => 'enterpriseMobile',
                    'editable' => true,
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
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

    /**
     * @param Operator $object
     *
     * @throws NonUniqueResultException
     */
    public function preUpdate($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $payslipOperatorDefaultLines = $object->getPayslipOperatorDefaultLines();
        foreach ($payslipOperatorDefaultLines as $payslipOperatorDefaultLine) {
            $amount = $payslipOperatorDefaultLine->getUnits() * $payslipOperatorDefaultLine->getPriceUnit();
            $payslipOperatorDefaultLine->setAmount($amount);
        }
        $this->em->flush();
    }

    public function preValidate(object $object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
