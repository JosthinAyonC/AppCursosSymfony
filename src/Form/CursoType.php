<?php

namespace App\Form;

use App\Entity\Curso;
use App\Entity\Usuario;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion', TextareaType::class)
            ->add('fechaPublicacion', DateTimeType::class)
            ->add('estado', ChoiceType::class, [
                'choices' => [
                    'En construccion' =>'C',
                    'Activo' => 'A',
                    'Inactivo' => 'I',                    
                ], 
                'expanded' => true
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Curso::class,
        ]);
    }
}
