<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('player', TextType::class, ['property_path' => 'player'])
            ->add('event_type', TextType::class, ['property_path' => 'eventType'])
            ->add('minute', IntegerType::class, ['property_path' => 'minute'])
            ->add('team_id', IntegerType::class, ['property_path' => 'teamId']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }

    protected function getEntityName(): string
    {
        return Event::class;
    }
}
