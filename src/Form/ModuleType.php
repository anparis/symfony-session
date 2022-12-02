<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModuleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {

    $builder
      ->add('nom', TextType::class)
      ->add('categorie', EntityType::class, [
        // looks for choices from this entity
        'class' => Categorie::class,

        'choice_label' => 'nom',

        // 'multiple' => true,
        // 'expanded' => true,
      ])
      ->add('submit', SubmitType::class, [
        'attr' => ['class' => 'btn']
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Module::class,
    ]);
  }
}
