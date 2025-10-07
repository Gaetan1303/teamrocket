<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{ArrayField, BooleanField, EmailField, IdField, TextField, AssociationField};
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return User::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield EmailField::new('email', 'E-mail');
        yield TextField::new('codename', 'Nom de code');
        yield ArrayField::new('roles', 'Rôles');
        yield TextField::new('password', 'Mot de passe')
            ->setFormType(RepeatedType::class, [
                'type'            => PasswordType::class,
                'first_options'   => ['label' => 'Mot de passe'],
                'second_options'  => ['label' => 'Répéter le mot de passe'],
                'mapped'          => false,
            ])
            ->onlyOnForms();
        yield AssociationField::new('teamVilain', 'Team');
        yield BooleanField::new('isVerified', 'Compte vérifié');
        yield TextField::new('starterPokemon', 'Starter')->setRequired(false);
        yield BooleanField::new('hasDoneFirstTheft', 'Premier vol effectué');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}