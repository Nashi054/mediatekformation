<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Niveau;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', null, [
                'label' => 'Date de publication',
                'required' => $options['require_publishedAt']
            ])
            ->add('title', null, [
                'label' => 'Titre',
                'required' => $options['require_title']
            ])
            ->add('description')
            ->add('miniature', null, [
                'label' => 'Miniature (max : 120x90)'
            ])
            ->add('picture', null, [
                'label' => 'Image (max : 640x480)'
            ])
            ->add('videoId', null, [
                'label' => 'Id de la vidéo'
            ])
            ->add('niveaux', EntityType::class, [
                'label' => 'Niveau',
                'required' => $options['require_niveau'],
                'class' => Niveau::class,
                'choice_label' => 'label'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
            'require_publishedAt' => false,
            'require_title' => false,
            'require_niveau' => false
        ]);
    }
}
