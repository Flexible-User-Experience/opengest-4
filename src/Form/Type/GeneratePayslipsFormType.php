<?php

namespace App\Form\Type;

use App\Entity\Operator\Operator;
use phpDocumentor\Reflection\Types\Collection;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GeneratePayslipsFormType.
 */
class GeneratePayslipsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'operators',
                EntityType::class,
                [
                    'label' => 'Operarios',
                    'required' => true,
                    'class' => Operator::class,
                    'multiple' => true,
                ]
            )
            ->add(
                'fromDate',
                DatePickerType::class,
                [
                    'label' => 'Desde',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('01/m/Y'),
                ]
            )
            ->add(
                'toDate',
                DatePickerType::class,
                [
                    'label' => 'Hasta',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('t/m/Y'),
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                array(
                    'label' => 'Crear',
                    'attr' => array(
                        'class' => 'btn btn-primary no-m-bottom',
                        'style' => 'margin-bottom: -15px',
                    ),
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_generate_payslips';
    }
}
