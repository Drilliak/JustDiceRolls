<?php

namespace AppBundle\Form;

use AppBundle\Entity\PlayerCharacter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PlayerCharacterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('backstory', TextareaType::class, ['required' => false, 'label' => "Histoire"])
            ->add('characteristics', CollectionType::class,
                ['entry_type'   => CharacteristicType::class,
                 'allow_add'    => false, 'allow_delete' => true,
                 'by_reference' => false,
                 'label'        => "Charactéristiques"])
            ->add('token', FileType::class, ['label' => 'Image token'])
            ->add('save', SubmitType::class, ['label' => 'Créer']);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => PlayerCharacter::class]);
    }
}