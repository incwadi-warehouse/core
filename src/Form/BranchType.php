<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Form;

use Incwadi\Core\Entity\Branch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BranchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('steps')
            ->add('currency')
            ->add('ordering')
            ->add('orderBy')
            ->add('public')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Branch::class,
        ]);
    }
}
