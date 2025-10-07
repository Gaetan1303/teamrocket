<?php

namespace App\Controller\Admin;

use App\Entity\Pokemon;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{ChoiceField, IdField, ImageField, IntegerField, TextField, ArrayField};

class PokemonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Pokemon::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Pokémon')
            ->setEntityLabelInPlural('Pokémon')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name', 'Nom');
        yield TextField::new('apiId', 'ID API')->onlyOnForms();
        yield ImageField::new('spriteFront', 'Sprite')
            ->setBasePath('uploads/sprites')
            ->setUploadDir('public/uploads/sprites')
            ->onlyOnForms();
        yield ArrayField::new('types', 'Types');
        yield IntegerField::new('baseExperience', 'Exp. de base');
        yield IntegerField::new('height', 'Taille (dm)');
        yield IntegerField::new('weight', 'Poids (hg)');
        yield IntegerField::new('hp', 'PV');
        yield IntegerField::new('attack', 'Attaque');
        yield IntegerField::new('defense', 'Défense');
        yield IntegerField::new('specialAttack', 'Att. Spé');
        yield IntegerField::new('specialDefense', 'Déf. Spé');
        yield IntegerField::new('speed', 'Vitesse');
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Sauvage' => Pokemon::STATUS_WILD,
                'Capturé' => Pokemon::STATUS_CAUGHT,
                'Volé'    => Pokemon::STATUS_STOLEN,
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}