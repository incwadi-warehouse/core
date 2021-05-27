<?php

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
    private AuthorToStringTransformer $authorToStringTransformer;

    private DateTimeToStringTransformer $dateTimeToStringTransformer;

    public function __construct(AuthorToStringTransformer $authorToStringTransformer, DateTimeToStringTransformer $dateTimeToStringTransformer)
    {
        $this->authorToStringTransformer = $authorToStringTransformer;
        $this->dateTimeToStringTransformer = $dateTimeToStringTransformer;
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
            ->add('type')
            ->add('lendTo')
            ->add('lendOn', TextType::class)
            ->add('cond')
            ->add('tags')
            ->add('recommendation')
        ;

        $builder->get('added')
            ->addModelTransformer($this->dateTimeToStringTransformer);
        $builder->get('author')
            ->addModelTransformer($this->authorToStringTransformer);
        $builder->get('lendOn')
            ->addModelTransformer($this->dateTimeToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
