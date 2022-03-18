<?php

namespace App\Form;

use App\Entity\Book;
use App\Form\DataTransformer\AuthorToStringTransformer;
use App\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function __construct(private readonly AuthorToStringTransformer $authorToStringTransformer, private readonly DateTimeToStringTransformer $dateTimeToStringTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('added', TextType::class)
            ->add('title')
            ->add('shortDescription')
            ->add('author', TextType::class)
            ->add('genre')
            ->add('price')
            ->add('sold')
            ->add('removed')
            ->add('reserved')
            ->add('releaseYear')
            ->add('cond')
            ->add('tags')
            ->add('recommendation')
            ->add('format')
        ;

        $builder->get('added')
            ->addModelTransformer($this->dateTimeToStringTransformer);
        $builder->get('author')
            ->addModelTransformer($this->authorToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
