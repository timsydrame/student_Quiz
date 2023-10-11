<?php

namespace App\Form;

use App\Entity\CandidateResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CandidateResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enoncer')
            ->add('imageResponse')
            ->add('iscorrect', CheckboxType::class, [
                'label' => 'Est-ce la bonne réponse?',
                'required' => false,
            ])
            ->add('question');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CandidateResponse::class,
        ]);
    }
}
