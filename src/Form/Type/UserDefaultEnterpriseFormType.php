<?php

namespace App\Form\Type;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserDefaultEnterpriseFormType.
 */
class UserDefaultEnterpriseFormType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $ts;

    /**
     * Methods.
     */

    /**
     * UserDefaultEnterpriseForm constructor.
     *
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $ts
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts)
    {
        $this->em = $em;
        $this->ts = $ts;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username',
                TextType::class,
                array(
                    'label' => 'Nom d\'usuari',
                    'disabled' => true,
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'label' => 'Email',
                    'disabled' => true,
                )
            )
            ->add(
                'firstname',
                TextType::class,
                array(
                    'label' => 'Nom',
                    'required' => true,
                )
            )
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'Cognoms',
                    'required' => true,
                )
            )
            ->add(
                'defaultEnterprise',
                EntityType::class,
                array(
                    'label' => 'Empresa',
                    'class' => Enterprise::class,
                    'query_builder' => $this->em->getRepository(Enterprise::class)->getEnterprisesByUserQB($this->ts->getToken()->getUser()),
                    'choice_label' => 'name',
                )
            )
            ->add(
                'mainImageFile',
                FileType::class,
                array(
                    'label' => ' ',
                    'required' => false,
                )
            )
            ->add(
                'send',
                SubmitType::class,
                array(
                    'label' => 'Actualitzar',
                    'attr' => array(
                        'class' => 'btn btn-success no-m-bottom',
                    ),
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'app_bundle_user_default_enterprise';
    }
}
