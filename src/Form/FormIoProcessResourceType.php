<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\FormIo;
use App\Entity\FormIoProcessResource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FormIoProcessResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path')
            ->add('resource', EntityType::class, [
                'class' => FormIo::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormIoProcessResource::class
        ]);
    }
}
