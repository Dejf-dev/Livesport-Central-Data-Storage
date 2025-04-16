<?php

namespace App\Form;

use App\Form\Request\TeamRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a Symfony form type for handling TeamRequest data.
 *
 * @package App\Form
 */
class TeamType extends AbstractType
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
            ->add('name', TextType::class, ['property_path' => 'name'])
            ->add('city', TextType::class, ['property_path' => 'city'])
            ->add('founded', IntegerType::class, ['property_path' => 'founded'])
            ->add('stadium', TextType::class, ['property_path' => 'stadium']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeamRequest::class,
            'csrf_protection' => false
        ]);
    }
}
