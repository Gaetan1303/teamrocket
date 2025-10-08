<?php
# src/Form/SbireType.php
namespace App\Form;

use App\Entity\Sbire;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class SbireType extends AbstractType
{
    public function __construct(private Security $security){}

    private function picturesForTeam(?string $teamCode): array
    {
        $pool = [
            'rocket' => [
                'rocket-m-1.png' => 'Rocket M 1',
                'rocket-f-1.png' => 'Rocket F 1',
                'rocket-m-2.png' => 'Rocket M 2',
                'rocket-f-2.png' => 'Rocket F 2',
            ],
            'aqua'   => [
                'aqua-m-1.png'   => 'Aqua M 1',
                'aqua-f-1.png'   => 'Aqua F 1',
            ],
            'magma'  => [
                'magma-m-1.png'  => 'Magma M 1',
                'magma-f-1.png'  => 'Magma F 1',
            ],
        ];
        return $pool[$teamCode] ?? $pool['rocket'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $team = $user?->getTeamVilain()?->getCode(); // "rocket"|"aqua"|"magma"

        $pictures = $this->picturesForTeam($team);

        $builder
            ->add('picture', ChoiceType::class, [
                'choices'      => $pictures,
                'expanded'     => true,
                'multiple'     => false,
                'label'        => 'Apparence',
                'data'         => array_key_first($pictures), // <- valeur par défaut
                'attr'         => ['class' => 'picture-choices'],
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur dominante',
                'data'  => '#000000', // <- valeur par défaut
                'attr'  => ['class' => 'color-picker'],
            ])
            ->add('power', RangeType::class, [
                'label' => 'Puissance (1-10)',
                'attr'  => ['min' => 1, 'max' => 10, 'class' => 'stat-range'],
            ])
            ->add('defense', RangeType::class, [
                'label' => 'Défense (1-10)',
                'attr'  => ['min' => 1, 'max' => 10, 'class' => 'stat-range'],
            ])
            ->add('speed', RangeType::class, [
                'label' => 'Vitesse (1-10)',
                'attr'  => ['min' => 1, 'max' => 10, 'class' => 'stat-range'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sbire::class,
        ]);
    }
}