<?php

namespace App\Form;

use App\Entity\CandidateResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CandidateResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enoncer', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre énoncé de reponse',
                ],
            ])
            // ->add('imageResponse', TextType::class, [
            //     'attr' => [
            //         'placeholder' => 'Ajoutez le lien de l\'image ici',
            //     ],
            // ])
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
