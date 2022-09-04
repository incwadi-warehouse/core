<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Form\DataTransformer\BookToStringTransformer;
use App\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function __construct(private readonly DateTimeToStringTransformer $dateTimeToStringTransformer, private readonly BookToStringTransformer $bookToStringTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('collection', TextType::class)
            ->add('notes')
            ->add('books', TextType::class)
            ->add('salutation')
            ->add('firstname')
            ->add('surname')
            ->add('mail')
            ->add('phone')
            ->add('open')
        ;

        $builder->get('collection')
            ->addModelTransformer($this->dateTimeToStringTransformer);
        $builder->get('books')
            ->addModelTransformer($this->bookToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
