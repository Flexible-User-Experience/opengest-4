<?php

namespace App\Form\Type;

use App\Entity\Operator\OperatorWorkRegisterHeader;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GenerateTimeSummaryFormType.
 */
class GenerateTimeSummaryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'operatorWorkRegisterHeaders',
                EntityType::class,
                [
                    'label' => 'Partes de trabajo',
                    'required' => true,
                    'class' => OperatorWorkRegisterHeader::class,
                    'multiple' => true,
                    'error_bubbling' => false,
                    'by_reference' => true,
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
                'percentage',
                NumberType::class,
                [
                    'label' => '%',
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
    public function getBlockPrefix()
    {
        return 'app_generate_time_summary';
    }
}
