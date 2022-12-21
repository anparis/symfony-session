<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Formateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('tel', TextType::class)
            ->add('date_naissance',DateType::class, [
              'widget' => 'single_text'
            ])
            ->add('ville', TextType::class)
            ->add('sexe', ChoiceType::class, [
              'choices'  => [
                  'homme' => 'homme',
                  'femme' => 'femme',
              ]
            ])
            ->add('modules', EntityType::class, [
              // looks for choices from this entity
              'class' => Module::class,
          
              // uses the User.username property as the visible option string
              // 'choice_label' => 'nom',
          
              // used to render a select box, check boxes or radios
              'multiple' => true,
              // 'expanded' => true,
          ])
            ->add('submit',SubmitType::class, [
              'attr' => ['class' => 'btn']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formateur::class,
        ]);
    }
}
