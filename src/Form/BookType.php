<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Form;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Form\DataTransformer\AuthorToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    private $authorToStringTransformer;


    public function __construct(AuthorToStringTransformer $authorToStringTransformer)
    {
        $this->authorToStringTransformer = $authorToStringTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('added', TextType::class)
            ->add('title')
            ->add('author', TextType::class)
            ->add('genre')
            ->add('price')
            ->add('stocked')
            ->add('yearOfPublication')
            ->add('type')
            ->add('premium')
            ->add('lendTo')
            ->add('lendOn', TextType::class)
        ;

        $builder->get('added')
            ->addModelTransformer(new CallbackTransformer(
                function ($date) {
                    if (!$date) {
                        return;
                    }
                    return (string)$date->getTimestamp();
                },
                function ($date) {
                    return $date ? new \DateTime('@' . $date) : new \DateTime();
                }
            ))
        ;
        $builder->get('author')
            ->addModelTransformer($this->authorToStringTransformer);
        $builder->get('lendOn')
            ->addModelTransformer(new CallbackTransformer(
                function ($date) {
                    if (!$date) {
                        return;
                    }
                    return (string)$date->getTimestamp();
                },
                function ($date) {
                    if (!$date) {
                        return;
                    }
                    return $date ? new \DateTime('@' . $date) : new \DateTime();
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
