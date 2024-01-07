<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['label' => 'Nom'])
            ->add('price', MoneyType::class, ['label' => 'Prix'])
            ->add('startAt', null, ['label' => 'Date de début'])
            ->add('place', null, [
                'label' => 'Pays',
                'choice_label' => 'city'
            ])
            ->add('posterFile', FileType::class, ['label' => 'Image'])
            ->add('categories', null, [
                'label' => 'Catégories',
                'choice_label' => 'name',
                'expanded' => true,
            ])
            ->add('endAt', null, ['label' => 'Date de fin'])
            // ->add('poster')
            // ->add('place', EntityType::class, [
            //     'class' => Place::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('categories', EntityType::class, [
            //     'class' => Category::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
