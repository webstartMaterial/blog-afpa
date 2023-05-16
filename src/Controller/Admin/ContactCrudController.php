<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('email'),
            TextField::new('message'),
            ChoiceField::new('object')->setChoices([
                'Je souhaite vous contacter par choix 1' => 'Choix 1',
                'Je souhaite vous contacter par choix 2' => 'Choix 2' ,
                'Je souhaite vous contacter par choix 3' => 'Choix 3'
            ]),
            TextField::new('phone'),
        ];
    }
    
}
