<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Form;

use Incwadi\Core\Entity\Reservation;
use Incwadi\Core\Form\DataTransformer\BookToStringTransformer;
use Incwadi\Core\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    private DateTimeToStringTransformer $dateTimeToStringTransformer;
    private BookToStringTransformer $bookToStringTransformer;

    public function __construct(DateTimeToStringTransformer $dateTimeToStringTransformer, BookToStringTransformer $bookToStringTransformer)
    {
        $this->dateTimeToStringTransformer = $dateTimeToStringTransformer;
        $this->bookToStringTransformer = $bookToStringTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('collection', TextType::class)
            ->add('notes')
            ->add('books', TextType::class)
        ;

        $builder->get('collection')
            ->addModelTransformer($this->dateTimeToStringTransformer);
        $builder->get('books')
            ->addModelTransformer($this->bookToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
