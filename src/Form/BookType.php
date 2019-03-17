<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Form;

use Baldeweg\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('added', TextType::class)
            ->add('author')
            ->add('genre')
            ->add('price')
            ->add('title')
            ->add('stocked')
            ->add('yearOfPublication')
            ->add('type')
            ->add('premium')
        ;

        $builder->get('added')
            ->addModelTransformer(new CallbackTransformer(
                function ($date) {
                    return (string)$date->getTimestamp();
                },
                function ($date) {
                    return new \DateTime('@' . $date);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class
        ]);
    }
}
