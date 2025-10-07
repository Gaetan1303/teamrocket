<?php

namespace App\Controller\Admin;

use Vich\UploaderBundle\Entity\File;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{DateTimeField, IdField, TextField, ImageField};

class FileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return File::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Fichier')
            ->setEntityLabelInPlural('Fichiers')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('originalName', 'Nom original');
        yield ImageField::new('path', 'Aperçu')
            ->setBasePath('uploads/files')
            ->onlyOnIndex();
        yield DateTimeField::new('updatedAt', 'Modifié le');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT); // on ne créé/édite pas un File
    }
}