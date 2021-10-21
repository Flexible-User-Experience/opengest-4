<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use App\Entity\Operator\Operator;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
     * @var string
     */
    protected $baseRoutePattern = 'operaris/operari';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'surname1',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('downloadProfilePhotoImage', $this->getRouterIdParameter().'/profilePhoto')
            ->add('batch')
            ->remove('delete');
    }

    /**
     * @param array $actions
     */
    protected function configureBatchActions($actions): array
    {
        if (
            $this->hasRoute('edit')
        ) {
            $actions['createPayslipFromOperators'] = [
                'label' => 'admin.action.generate_payslips_from_selected',
                'ask_confirmation' => true,
            ];
        }

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Informació')
                ->with('General', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'profilePhotoImageFile',
                        FileType::class,
                        [
                            'label' => 'Imatge',
                            'help' => $this->getProfileHelperFormMapperWithThumbnail(),
                            'required' => false,
                        ]
                    )
                    ->add(
                        'taxIdentificationNumber',
                        null,
                        [
                            'label' => 'DNI/NIE',
                            'required' => true,
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
                        'surname1',
                        null,
                        [
                            'label' => 'Primer cognom',
                        ]
                    )
                    ->add(
                        'surname2',
                        null,
                        [
                            'label' => 'Segon cognom',
                        ]
                    )
                ->end()
                ->with('Contacte', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'email',
                        null,
                        [
                            'label' => 'Email',
                        ]
                    )
                    ->add(
                        'address',
                        null,
                        [
                            'label' => 'Adreça',
                            'required' => true,
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
                        'enterpriseMobile',
                        null,
                        [
                            'label' => 'Mòbil d\'empresa',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'ownPhone',
                        null,
                        [
                            'label' => 'Telèfon personal',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'ownMobile',
                        null,
                        [
                            'label' => 'Mòbil personal',
                            'required' => true,
                        ]
                    )
                ->end()
                ->with('Llicència', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'hasCarDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'Llicència conducció de cotxe',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'hasLorryDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'Llicència conducció de camions',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'hasTowingDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'Llicència conducció de remolc',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'hasCraneDrivingLicense',
                        CheckboxType::class,
                        [
                            'label' => 'Llicència conducció de grua',
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Controls', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'enterpriseGroupBounty',
                        EntityType::class,
                        [
                            'class' => EnterpriseGroupBounty::class,
                            'label' => 'Grup prima',
                            'required' => true,
                            'query_builder' => $this->rm->getEnterpriseGroupBountyRepository()->getEnabledSortedByNameQB(),
                        ]
                    )
                    ->add(
                        'brithDate',
                        DatePickerType::class,
                        [
                            'label' => 'Data de naixement',
                            'format' => 'd/M/y',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'registrationDate',
                        DatePickerType::class,
                        [
                            'label' => 'Data de registre',
                            'format' => 'd/M/y',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'bancAccountNumber',
                        null,
                        [
                            'label' => 'No. de compte bancari',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'socialSecurityNumber',
                        null,
                        [
                            'label' => 'No. de Seguretat Social',
                            'required' => true,
                        ]
                    )
                    ->add(
                        'hourCost',
                        null,
                        [
                            'label' => 'Cost hora',
                            'required' => true,
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
                ->with('EPI\'s', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'shoeSize',
                        null,
                        [
                            'label' => 'Mida de sabata',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'jerseytSize',
                        null,
                        [
                            'label' => 'Mida de jersei',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'jacketSize',
                        null,
                        [
                            'label' => 'Mida de jaqueta',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'tShirtSize',
                        null,
                        [
                            'label' => 'Mida de camisa',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'pantSize',
                        null,
                        [
                            'label' => 'Mida de pantaló',
                            'required' => false,
                        ]
                    )
                    ->add(
                        'workingDressSize',
                        null,
                        [
                            'label' => 'Mida de roba de treball',
                            'required' => false,
                        ]
                    )
                ->end()
            ->end()
            ->tab('Recursos')
                ->with('No. d\'identificació fiscal', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'taxIdentificationNumberImageFile',
                        FileType::class,
                        [
                            'label' => 'DNI/NIE',
                            'help' => $this->getSmartHelper('getTaxIdentificationNumberImage', 'taxIdentificationNumberImageFile'),
                            'required' => false,
                        ]
                    )
                    ->end()
                ->with('Seguretat Social', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'dischargeSocialSecurityImageFile',
                        FileType::class,
                        [
                            'label' => 'Baixa Seguretat Social',
                            'help' => $this->getSmartHelper('getDischargeSocialSecurityImage', 'dischargeSocialSecurityImageFile'),
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Contracte de treball', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'employmentContractImageFile',
                        FileType::class,
                        [
                            'label' => 'Contracte',
                            'help' => $this->getSmartHelper('getEmploymentContractImage', 'employmentContractImageFile'),
                            'required' => false,
                        ]
                    )
                    ->end()
                ->with('Informe mèdic', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'medicalCheckImageFile',
                    FileType::class,
                    [
                        'label' => 'Revisió mèdica',
                        'help' => $this->getSmartHelper('getMedicalCheckImage', 'medicalCheckImageFile'),
                        'required' => false,
                    ]
                )
                ->end()
                ->with('EPI\'s', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'episImageFile',
                        FileType::class,
                        [
                            'label' => 'EPI',
                            'help' => $this->getSmartHelper('getEpisImage', 'episImageFile'),
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Formació', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'trainingDocumentImageFile',
                        FileType::class,
                        [
                            'label' => 'Títol de formació',
                            'help' => $this->getSmartHelper('getTrainingDocumentImage', 'trainingDocumentImageFile'),
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Altres Documents', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'informationImageFile',
                        FileType::class,
                        [
                            'label' => 'Altra informació',
                            'help' => $this->getSmartHelper('getInformationImage', 'informationImageFile'),
                            'required' => false,
                        ]
                    )
                ->end()
                ->with('Llicències', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'drivingLicenseImageFile',
                        FileType::class,
                        [
                            'label' => 'Carnet de conduir',
                            'help' => $this->getSmartHelper('getDrivingLicenseImage', 'drivingLicenseImageFile'),
                            'required' => false,
                        ]
                    )
                    ->add(
                        'useOfMachineryAuthorizationImageFile',
                        FileType::class,
                        [
                            'label' => 'Autorització de maquinària',
                            'help' => $this->getSmartHelper('getUseOfMachineryAuthorizationImage', 'useOfMachineryAuthorizationImageFile'),
                            'required' => false,
                        ]
                    )
                    ->add(
                        'cranesOperatorLicenseImageFile',
                        FileType::class,
                        [
                            'label' => 'Llicència d\'operari',
                            'help' => $this->getSmartHelper('getCranesOperatorLicenseImage', 'cranesOperatorLicenseImageFile'),
                            'required' => false,
                        ]
                    )
                ->end()
            ->end()
            ->tab('Nóminas')
                ->with('Conceptos por defecto', $this->getFormMdSuccessBoxArray(12))
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
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'taxIdentificationNumber',
                null,
                [
                    'label' => 'DNI/NIE',
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
                'surname1',
                null,
                [
                    'label' => 'Primer cognom',
                ]
            )
            ->add(
                'enterpriseGroupBounty',
                null,
                [
                    'label' => 'Grup prima',
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
                'enabled',
                null,
                [
                    'label' => 'Actiu',
                ]
            )
        ;
    }

    protected function configureDefaultFilterValues(array &$filterValues)
    {
        $filterValues['enabled'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_YES,
        ];
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
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'profilePhotoImage',
                null,
                [
                    'label' => 'Imatge',
                    'template' => 'admin/cells/list__cell_profile_image_field.html.twig',
                ]
            )
            ->add(
                'taxIdentificationNumber',
                null,
                [
                    'label' => 'DNI/NIE',
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
                'surname1',
                null,
                [
                    'label' => 'Primer cognom',
                    'editable' => true,
                ]
            )
            ->add(
                'surname2',
                null,
                [
                    'label' => 'Segon cognom',
                    'editable' => true,
                ]
            )
            ->add(
                'enterprise_mobile',
                null,
                [
                    'label' => 'Mòbil empresa',
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
    public function preUpdate($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $payslipOperatorDefaultLines = $object->getPayslipOperatorDefaultLines();
        foreach ($payslipOperatorDefaultLines as $payslipOperatorDefaultLine) {
            $amount = $payslipOperatorDefaultLine->getUnits() * $payslipOperatorDefaultLine->getPriceUnit();
            $payslipOperatorDefaultLine->setAmount($amount);
        }
        $this->em->flush();
    }
}
