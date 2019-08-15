<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Form;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Form\DataTransformer\AuthorToStringTransformer;
use Incwadi\Core\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    private $authorToStringTransformer;

    private $dateTimeToStringTransformer;

    public function __construct(AuthorToStringTransformer $authorToStringTransformer, DateTimeToStringTransformer $dateTimeToStringTransformer)
    {
        $this->authorToStringTransformer = $authorToStringTransformer;
        $this->dateTimeToStringTransformer = $dateTimeToStringTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('added', TextType::class)
            ->add('title')
            ->add('author', TextType::class)
            ->add('genre')
            ->add('price')
            ->add('sold')
            ->add('removed')
            ->add('releaseYear')
            ->add('type')
            ->add('premium')
            ->add('lendTo')
            ->add('lendOn', TextType::class)
        ;

        $builder->get('added')
            ->addModelTransformer($this->dateTimeToStringTransformer);
        $builder->get('author')
            ->addModelTransformer($this->authorToStringTransformer);
        $builder->get('lendOn')
            ->addModelTransformer($this->dateTimeToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class
        ]);
    }
}
