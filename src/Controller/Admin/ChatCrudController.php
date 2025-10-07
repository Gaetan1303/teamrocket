<?php

namespace App\Controller\Admin;

use App\Entity\Chat;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{DateTimeField, IdField, TextareaField, AssociationField};

class ChatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Chat::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message')
            ->setEntityLabelInPlural('Messages')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield AssociationField::new('user', 'Auteur');
        yield TextareaField::new('message', 'Contenu');
        yield DateTimeField::new('createdAt', 'Date')->onlyOnIndex();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT); // On ne crée/édite pas un message
    }
}