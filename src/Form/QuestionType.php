<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('enonce', null, [
            // 'label' => 'Question',
            'attr' => [
                'placeholder' => ' Ajouter Votre question',
            ],
        ])
            // ->add('image')
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'name', // Remplacez 'nom' par le champ de votre entité Quiz que vous souhaitez afficher dans le choix
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
