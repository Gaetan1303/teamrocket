<?php

namespace App\Controller\Admin;

use App\Entity\TeamVilain;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{ColorField, IdField, TextareaField, TextField, AssociationField};

class TeamVilainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return TeamVilain::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Team')
            ->setEntityLabelInPlural('Teams de Vilains')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name', 'Nom');
        yield TextField::new('region', 'Région')->setRequired(false);
        yield TextareaField::new('credo', 'Credo');
        yield ColorField::new('colorCode', 'Couleur')->setRequired(false);
        yield AssociationField::new('sbires', 'Sbires')
            ->onlyOnDetail(); // on les voit dans la fiche détail
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}