<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\GuideBook;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubGuideType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('translations', TranslationsType::class, [
            'locales' => ['en', 'uk',],   // [1]
            'help' => 'Title of sub guide',
            'default_locale' => ['en'],              // [1]
            'required_locales' => ['en', 'uk'],            // [1]
            'fields' => [                               // [2]
                'title' => [                         // [3.a]
                    'label' => 'Title.',                    // [4]
                ]
            ],
        ])
            ->add('enable');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GuideBook::class
        ]);
    }
}