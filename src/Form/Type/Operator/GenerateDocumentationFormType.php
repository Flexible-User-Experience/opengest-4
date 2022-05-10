<?php

namespace App\Form\Type\Operator;

use App\Entity\Operator\Operator;
use App\Enum\EnterpriseDocumentsEnum;
use App\Enum\OperatorDocumentsEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GenerateDocumentationFormType.
 */
class GenerateDocumentationFormType extends AbstractType
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
                    'error_bubbling' => false,
                    'by_reference' => true,
                ]
            )
            ->add(
                'documentation',
                ChoiceType::class,
                [
                    'label' => 'Documentos de operario',
                    'choices' => OperatorDocumentsEnum::getEnumArray(),
                    'multiple' => true,
                    'translation_domain' => 'admin',
                    'expanded' => true,
                ]
            )
            ->add(
                'enterpriseDocumentation',
                ChoiceType::class,
                [
                    'label' => 'Documentos de empresa',
                    'choices' => EnterpriseDocumentsEnum::getEnumArray(),
                    'multiple' => true,
                    'translation_domain' => 'admin',
                    'expanded' => true,
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
        return 'app_generate_payslips';
    }
}
