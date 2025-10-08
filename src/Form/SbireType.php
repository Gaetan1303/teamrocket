<?php
// src/Form/SbireType.php
namespace App\Form;

use App\Entity\Sbire;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SbireType extends AbstractType
{
    private Security $security;
    private ParameterBagInterface $params;

    public function __construct(Security $security, ParameterBagInterface $params)
    {
        $this->security = $security;
        $this->params   = $params;
    }

    /* -------------------------------------------------- */
    /* buildForm                                          */
    /* -------------------------------------------------- */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $base = 'assets/images/characters/';

        /* ******  HOMME / FEMME POUR SA TEAM UNIQUEMENT  ****** */
        $team       = $user?->getTeamVilain();
        $prefix     = $team ? str_replace(' ', '-', strtolower($team->getName())) : 'default';
        $root       = $this->params->get('kernel.project_dir') . '/public/' . $base;
        $hommePath  = $base . $prefix . '-homme.png';
        $femmePath  = $base . $prefix . '-femme.png';
        $hasHomme   = file_exists($root . $prefix . '-homme.png');
        $hasFemme   = file_exists($root . $prefix . '-femme.png');

        // On crée **toujours** deux options
        $teamChoices = [];
        if ($hasHomme) {
            $teamChoices['Homme'] = $hommePath;
        } else {
            $teamChoices['Homme'] = $hasFemme ? $femmePath : $base.'default-homme.png';
        }
        if ($hasFemme) {
            $teamChoices['Femme'] = $femmePath;
        } else {
            $teamChoices['Femme'] = $hasHomme ? $hommePath : $base.'default-femme.png';
        }

        /* ****  DEBUG  (à retirer en prod)  **** */
        dump([
            'team'   => $team?->getName(),
            'prefix' => $prefix,
            'hasH'   => $hasHomme,
            'hasF'   => $hasFemme,
            'choices'=> $teamChoices,
        ]);
        /* *************************************** */

        /* ******  FORMULAIRE  ****** */
        $builder
            ->add('avatarType', ChoiceType::class, [
                'label'    => 'Type d\'avatar',
                'choices'  => [
                    'Avatar par défaut'   => 'default',
                    'Avatar de team'      => 'team',
                    'Avatar personnalisé' => 'custom',
                ],
                'expanded' => true,
                'multiple' => false,
                'data'     => 'default',
                'mapped'   => false,
            ])
            ->add('defaultAvatar', ChoiceType::class, [
                'label'    => 'Avatar par défaut',
                'choices'  => [
                    'Homme' => $base.'default-homme.png',
                    'Femme' => $base.'default-femme.png',
                ],
                'expanded' => true,
                'multiple' => false,
                'data'     => $base.'default-homme.png',
                'required' => false,
                'mapped'   => false,
            ])
            ->add('teamAvatar', ChoiceType::class, [
                'label'    => 'Avatar de team',
                'choices'  => $teamChoices,
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'mapped'   => false,
            ])
            ->add('customAvatar', FileType::class, [
                'label'       => 'Uploader ton avatar',
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => '2M',
                        'mimeTypes' => ['image/jpeg','image/png','image/gif','image/webp'],
                        'mimeTypesMessage' => 'Formats acceptés : JPEG, PNG, GIF, WEBP uniquement.',
                    ])
                ],
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur dominante',
                'data'  => '#000000',
            ])
            ->add('power', RangeType::class, [
                'label' => 'Puissance (1-10)',
                'attr'  => ['min' => 1, 'max' => 10],
            ])
            ->add('defense', RangeType::class, [
                'label' => 'Défense (1-10)',
                'attr'  => ['min' => 1, 'max' => 10],
            ])
            ->add('speed', RangeType::class, [
                'label' => 'Vitesse (1-10)',
                'attr'  => ['min' => 1, 'max' => 10],
            ]);
    }

    /* -------------------------------------------------- */
    /* configureOptions                                   */
    /* -------------------------------------------------- */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sbire::class,
        ]);
    }
}