<?php

namespace App\Form;

use App\Entity\FootballMatch;
use App\Form\Request\FootballMatchRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a Symfony form type for handling FootballMatchRequest data.
 *
 * @package App\Form
 */
class FootballMatchType extends AbstractType
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
            ->add('match_date', TextType::class, ['property_path' => 'matchDate'])
            ->add('stadium', TextType::class, ['property_path' => 'stadium'])
            ->add('score_home', IntegerType::class, ['property_path' => 'scoreHome'])
            ->add('score_away', IntegerType::class, ['property_path' => 'scoreAway'])
            ->add('home_team_id', IntegerType::class, ['property_path' => 'homeTeamId'])
            ->add('away_team_id', IntegerType::class, ['property_path' => 'awayTeamId']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FootballMatchRequest::class,
            'csrf_protection' => false
        ]);
    }
}
