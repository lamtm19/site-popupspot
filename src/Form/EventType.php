<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Titre'])
            ->add('description', null, ['label' => 'Description'])
            ->add('eventDate', null, ['label' => 'Date et heure'])
            ->add('zipcode', null, ['label' => 'Code postal'])
            ->add('city', null, ['label' => 'Ville'])
            ->add('region', null, ['label' => 'Région'])
            ->add('country', null, ['label' => 'Pays'])
            ->add('instagram', null, ['label' => 'Lien Instagram'])
            ->add('image', null, ['label' => 'Affiche / Image (URL)']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
