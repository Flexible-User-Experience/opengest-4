<?php

namespace App\Form\Type;

use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\SaleInvoiceSeries;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GenerateSaleInvoicesFormType.
 */
class GenerateSaleInvoicesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'saleDeliveryNotes',
                EntityType::class,
                [
                    'label' => 'Albaranes',
                    'required' => true,
                    'class' => SaleDeliveryNote::class,
                    'multiple' => true,
                    'error_bubbling' => false,
                    'by_reference' => true,
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'Fecha Factura',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('01/m/Y'),
                ]
            )
            ->add(
                'series',
                EntityType::class,
                [
                    'label' => 'Serie factura',
                    'class' => SaleInvoiceSeries::class,
                    'required' => true,
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => 'Crear',
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
        return 'app_generate_sale_invoices';
    }
}
