<?php

namespace App\Form;

use App\Entity\PossibleResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PossibleResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enonce')
            ->add('imageResponse')
            ->add('isCorrecte', CheckboxType::class, [
                'label' => 'Est ce la bonne reponse?',
                'required' => false, // Set to true if it's a mandatory field
            ])
            ->add('question');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PossibleResponse::class,
        ]);
    }
}
