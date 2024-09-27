<?php

namespace App\Form\Type;

use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Payslip\Payslip;
use App\Repository\Enterprise\EnterpriseTransferAccountRepository;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GeneratePaymentDocumentsPayslipFormType.
 */
class GeneratePaymentDocumentsPayslipFormType extends AbstractType
{
    private EnterpriseTransferAccountRepository $enterpriseTransferAccountRepository;

    protected TokenStorageInterface $tokenStorage;

    public function __construct(EnterpriseTransferAccountRepository $enterpriseTransferAccountRepository, TokenStorageInterface $tokenStorage)
    {
        $this->enterpriseTransferAccountRepository = $enterpriseTransferAccountRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'payslips',
                EntityType::class,
                [
                    'label' => 'Nóminas',
                    'required' => true,
                    'class' => Payslip::class,
                    'multiple' => true,
                    'error_bubbling' => false,
                    'by_reference' => true,
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'Fecha de pago',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('01/m/Y'),
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'Documento a generar',
                    'choices' => [
                        'Nóminas' => 'payslips',
                        'Dietas' => 'expenses',
                        'Recibos otros costes' => 'otherExpensesReceipts',
                        'Recibos de dietas' => 'expensesReceipts',
                    ],
                ]
            )
            ->add(
                'enterpriseTransferAccount',
                EntityType::class,
                [
                    'label' => 'Banco transferencia',
                    'class' => EnterpriseTransferAccount::class,
                    'query_builder' => $this->enterpriseTransferAccountRepository->getFilteredByEnterpriseEnabledSortedByNameQB($this->tokenStorage->getToken()->getUser()->getLoggedEnterprise()),
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => 'Generar',
                    'attr' => [
                        'class' => 'btn btn-primary no-m-bottom',
                        'style' => 'margin-bottom: -15px',
                    ],
                ]
            )
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'app_generate_payslip_payment_document';
    }
}
