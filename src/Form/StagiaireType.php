<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('tel')
            ->add('date_naissance',DateType::class, [
              'widget' => 'single_text'
            ])
            ->add('ville')
            ->add('sexe', ChoiceType::class, [
              'choices'  => [
                  'homme' => 'homme',
                  'femme' => 'femme',
              ]
            ])
            ->add('nom')
            ->add('prenom')
            // ->add('sessions',EntityType::class,[
            //   'class' => Session::class,
            //   'choice_label' => 'titre',
            // ])
            ->add('submit',SubmitType::class, [
              'attr' => ['class' => 'btn btn-2']
          ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
