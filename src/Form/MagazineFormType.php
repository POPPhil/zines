<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Magazine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MagazineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du magazine",
                'required' => False,
            ])
            ->add('description', TextareaType::class, [
                'label' => "Déscription du magazine",
                'required' => False,
            ])
            ->add('price', MoneyType::class, [
                'label' => "Prix du magazine",
                'required' => False,
                'currency' => 'EUR'
            ])
            ->add('created_at', DateType::class, [
                'label' => 'Date de sortie',
                'required' => false,
                'input' => 'datetime_immutable',
                'widget' => 'single_text' // input type="date"
            ])
            ->add('category', EntityType::class, [
                'label' => "Categorie",
                'required' => False,
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'imagine_pattern' => 'vignette', // Applique une configuration LiipImagine sur l'image
                'download_label' => false, // Enlève le lien de téléchargement
                'label' => 'Image de couverture',
                'delete_label' => 'Cocher pour supprimer cette image',
                /* 'attr' => [
                    'class' => 'img-fluid rounded'
                ] */
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-success',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Magazine::class,
        ]);
    }
}
