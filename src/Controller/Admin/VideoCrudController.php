<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VideoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Video::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            AssociationField::new('category'),
            AssociationField::new('genres')
                ->hideOnIndex(),
            ArrayField::new('genres')
                ->onlyOnIndex(),

            TextField::new('name'),
            ImageField::new('image')
                ->onlyOnIndex()
                ->setBasePath($this->getParameter('app.path.video_images')),
            UrlField::new('path'),
            IntegerField::new('duration'),
            NumberField::new('rating'),
            TextareaField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->hideOnIndex(),
            IntegerField::new('release_year'),
            TextField::new('director'),
            ArrayField::new('cast'),

            TextField::new('country')




        ];
    }
}
