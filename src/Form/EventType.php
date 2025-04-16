<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a Symfony form type for handling EventRequest data.
 *
 * @package App\Form
 */
class EventType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array $options An array of options for the form.
     */
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

    /**
     * Returns the name of the associated entity class.
     *
     * @return string The entity class name.
     */
    protected function getEntityName(): string
    {
        return Event::class;
    }
}
