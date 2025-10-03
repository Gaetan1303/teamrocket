<?php

namespace App\Form;

use App\Entity\Sbire;
use App\Entity\TeamVilain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('codename') // pseudo unique

            // Liste déroulante des équipes
            ->add('teamVilain', EntityType::class, [
                'class'        => TeamVilain::class,
                'choice_label' => 'name',
                'placeholder'  => 'Choisis ton équipe',
                'constraints'  => new NotBlank(),
            ])

            // Mot de passe + confirmation
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'first_options'   => ['label' => 'Mot de passe'],
                'second_options'  => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'mapped'          => false,
                'attr'            => ['autocomplete' => 'new-password'],
                'constraints'     => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min'        => 6,
                        'max'        => 4096,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sbire::class,
        ]);
    }
}