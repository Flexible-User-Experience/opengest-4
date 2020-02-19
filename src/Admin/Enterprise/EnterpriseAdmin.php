<?php

namespace App\Admin\Enterprise;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Setting\User;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
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
     * @var string
     */
    protected $baseRoutePattern = 'empreses/empresa';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    );

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
            ->add('change', $this->getRouterIdParameter().'/change-user-default-enterprise')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Informació')
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'logoFile',
                    FileType::class,
                    array(
                        'label' => 'Logo',
                        'help' => $this->getLogoHelperFormMapperWithThumbnail(),
                        'required' => false,
                    )
                )
                ->add(
                    'taxIdentificationNumber',
                    null,
                    array(
                        'label' => 'CIF',
                    )
                )
                ->add(
                    'name',
                    null,
                    array(
                        'label' => 'Nom',
                    )
                )
                ->add(
                    'businessName',
                    null,
                    array(
                        'label' => 'Nom fiscal',
                    )
                )
                ->end()
                ->with('Contacte', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'address',
                    null,
                    array(
                        'label' => 'Adreça',
                    )
                )
                ->add(
                    'city',
                    null,
                    array(
                        'label' => 'Ciutat',
                        'required' => true,
                    )
                )
                ->add(
                    'email',
                    null,
                    array(
                        'label' => 'Email',
                    )
                )
                ->add(
                    'www',
                    null,
                    array(
                        'label' => 'Web corporativa',
                    )
                )
                ->add(
                    'phone1',
                    null,
                    array(
                        'label' => 'Telèfon 1',
                    )
                )
                ->add(
                    'phone2',
                    null,
                    array(
                        'label' => 'Telèfon 2',
                    )
                )
                ->add(
                    'phone3',
                    null,
                    array(
                        'label' => 'Telèfon 3',
                    )
                )
                ->end()
                ->with('Controls', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'users',
                    EntityType::class,
                    array(
                        'label' => 'Usuaris',
                        'required' => false,
                        'class' => User::class,
                        'choice_label' => 'fullname',
                        'multiple' => true,
                        'query_builder' => $this->rm->getUserRepository()->getEnabledSortedByNameQB(),
                        'by_reference' => false,
                    )
                )
                ->add(
                    'enabled',
                    CheckboxType::class,
                    array(
                        'label' => 'Actiu',
                        'required' => false,
                    )
                )
                ->end()
            ->end()
            ->tab('Recursos')
                ->with('TC\'s', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'tc1ReceiptFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut TC1',
                        'help' => $this->getSmartHelper('getTc1Receipt', 'tc1ReceiptFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'tc2ReceiptFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut TC2',
                        'help' => $this->getSmartHelper('getTc2Receipt', 'tc2ReceiptFile'),
                        'required' => false,
                    )
                )
                ->end()
                ->with('Seguretat Social', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'ssRegistrationFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut SS registre',
                        'help' => $this->getSmartHelper('getSsRegistration', 'ssRegistrationFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'ssPaymentCertificateFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut pagament certificat',
                        'help' => $this->getSmartHelper('getSsPaymentCertificate', 'ssPaymentCertificateFile'),
                        'required' => false,
                    )
                )
                ->end()
                ->with('Responsabilitat Civil', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'rc1InsuranceFile',
                    FileType::class,
                    array(
                        'label' => 'RC 1',
                        'help' => $this->getSmartHelper('getRc1Insurance', 'rc1InsuranceFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'rc2InsuranceFile',
                    FileType::class,
                    array(
                        'label' => 'RC 2',
                        'help' => $this->getSmartHelper('getRc2Insurance', 'rc2InsuranceFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'rcReceiptFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut RC',
                        'help' => $this->getSmartHelper('getRcReceipt', 'rcReceiptFile'),
                        'required' => false,
                    )
                )
                ->end()
                ->with('Riscos Laborals', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'preventionServiceContractFile',
                    FileType::class,
                    array(
                        'label' => 'Contracte',
                        'help' => $this->getSmartHelper('getPreventionServiceContract', 'preventionServiceContractFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'preventionServiceInvoiceFile',
                    FileType::class,
                    array(
                        'label' => 'Factura',
                        'help' => $this->getSmartHelper('getPreventionServiceInvoice', 'preventionServiceInvoiceFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'preventionServiceReceiptFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut',
                        'help' => $this->getSmartHelper('getPreventionServiceReceipt', 'preventionServiceReceiptFile'),
                        'required' => false,
                    )
                )
                ->end()
                ->with('Assegurances', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'occupationalAccidentsInsuranceFile',
                    FileType::class,
                    array(
                        'label' => 'Assegurança d\'accident de treball',
                        'help' => $this->getSmartHelper('getOccupationalAccidentsInsurance', 'occupationalAccidentsInsuranceFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'occupationalReceiptFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut',
                        'help' => $this->getSmartHelper('getOccupationalReceipt', 'occupationalReceiptFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'laborRiskAssessmentFile',
                    FileType::class,
                    array(
                        'label' => 'Avaluació riscos',
                        'help' => $this->getSmartHelper('getLaborRiskAssessment', 'laborRiskAssessmentFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'securityPlanFile',
                    FileType::class,
                    array(
                        'label' => 'Pla seguretat',
                        'help' => $this->getSmartHelper('getSecurityPlan', 'securityPlanFile'),
                        'required' => false,
                    )
                )
                ->end()
                ->with('Impost d\'Activitats Econòmiques', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'iaeRegistrationFile',
                    FileType::class,
                    array(
                        'label' => 'Alta IAE',
                        'help' => $this->getSmartHelper('getIaeRegistration', 'iaeRegistrationFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'iaeReceiptFile',
                    FileType::class,
                    array(
                        'label' => 'Rebut IAE',
                        'help' => $this->getSmartHelper('getIaeReceipt', 'iaeReceiptFile'),
                        'required' => false,
                    )
                )
                ->end()
                ->with('Altres Documents', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'deedOfIncorporationFile',
                    FileType::class,
                    array(
                        'label' => 'Escritura constitució',
                        'help' => $this->getSmartHelper('getDeedOfIncorporation', 'deedOfIncorporationFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'taxIdentificationNumberCardFile',
                    FileType::class,
                    array(
                        'label' => 'Carta CIF',
                        'help' => $this->getSmartHelper('getTaxIdentificationNumberCard', 'taxIdentificationNumberCardFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'reaCertificateFile',
                    FileType::class,
                    array(
                        'label' => 'Certificat REA',
                        'help' => $this->getSmartHelper('getReaCertificate', 'reaCertificateFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'oilCertificateFile',
                    FileType::class,
                    array(
                        'label' => 'Certificat recullida d\'oli',
                        'help' => $this->getSmartHelper('getOilCertificate', 'oilCertificateFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'gencatPaymentCertificateFile',
                    FileType::class,
                    array(
                        'label' => 'Certificat pagament Generalitat',
                        'help' => $this->getSmartHelper('getGencatPaymentCertificate', 'gencatPaymentCertificateFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'deedsOfPowersFile',
                    FileType::class,
                    array(
                        'label' => 'Escriptura de poder',
                        'help' => $this->getSmartHelper('getDeedsOfPowers', 'deedsOfPowersFile'),
                        'required' => false,
                    )
                )
                ->add(
                    'mutualPartnershipFile',
                    FileType::class,
                    array(
                        'label' => 'Document associació a mutua',
                        'help' => $this->getSmartHelper('getMutualPartnership', 'mutualPartnershipFile'),
                        'required' => false,
                    )
                )
                ->end()
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
                'taxIdentificationNumber',
                null,
                array(
                    'label' => 'CIF',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Email',
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'Ciutat',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
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
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->join($queryBuilder->getRootAliases()[0].'.users', 'u')
                ->andWhere('u.id = :id')
                ->setParameter('id', $this->getUserLogedId())
            ;
        }

        return $queryBuilder;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'logo',
                null,
                array(
                    'label' => 'Logo',
                    'template' => '::Admin/Cells/list__cell_logo_image_field.html.twig',
                )
            )
            ->add(
                'taxIdentificationNumber',
                null,
                array(
                    'label' => 'CIF',
                    'editable' => true,
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
                'email',
                null,
                array(
                    'label' => 'Email',
                    'editable' => true,
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'Ciutat',
                    'editable' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array('template' => '::Admin/Buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                    ),
                    'label' => 'admin.actions',
                )
            )
        ;
    }
}
