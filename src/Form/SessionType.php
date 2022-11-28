<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('titre',TextType::class)
      ->add('nb_places')
      ->add('date_debut',DateType::class, [
        'widget' => 'single_text'
      ])
      ->add('date_fin',DateType::class, [
        'widget' => 'single_text'
      ])
      ->add('stagiaires',CollectionType::class,[
        'entry_type' => Stagiaire::class,
        // 'choice_label' => 'raisonSociale',
      ])
      ->add('submit',SubmitType::class, [
        'attr' => ['class' => 'btn']
    ])
  ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Session::class,
    ]);
  }
}
