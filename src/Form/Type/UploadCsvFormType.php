<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class UploadCsvFormType.
 */
class UploadCsvFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'csvFile',
                FileType::class,
                array(
                    'label' => 'Fichero',
                    'required' => true,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'text/csv',
                                'text/plain',
                            ],
//                            'mimeTypesMessage' => 'El fichero tiene que ser un .csv',
                        ])
                    ],
                )
            )
            ->add(
                'upload',
                SubmitType::class,
                array(
                    'label' => 'Subir',
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
    public function getBlockPrefix(): string
    {
        return 'app_upload_csv_type';
    }
}
