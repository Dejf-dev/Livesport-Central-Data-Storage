<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
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
            'data_class' => Team::class,
        ]);
    }

    protected function getEntityName(): string
    {
        return Team::class;
    }
}
