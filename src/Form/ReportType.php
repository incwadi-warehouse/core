<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Form;

use Incwadi\Core\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('searchTerm')
            ->add('limitTo')
            ->add('sold')
            ->add('removed')
            ->add('olderThenXMonths')
            ->add('branches')
            ->add('genres')
            ->add('lendMoreThenXMonths')
            ->add('orderBy')
            ->add('releaseYear')
            ->add('type')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
