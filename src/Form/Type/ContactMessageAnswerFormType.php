<?php

namespace App\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class ContactMessageAnswerFormType.
 *
 * @category FormType
 *
 * @author   David Romaní <david@flux.cat>
 */
class ContactMessageAnswerFormType extends ContactMessageFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'answer',
                TextareaType::class,
                array(
                    'label' => 'admin.label.answer',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'attr' => array(
                        'rows' => 6,
                    ),
                )
            )
            ->add(
                'send',
                SubmitType::class,
                array(
                    'label' => 'admin.label.send',
                    'translation_domain' => 'admin',
                    'attr' => array(
                        'class' => 'btn-primary',
                    ),
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'contact_message_answer';
    }
}
