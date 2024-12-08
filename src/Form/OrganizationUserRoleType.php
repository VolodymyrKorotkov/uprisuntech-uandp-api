<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\OrganizationUserRole;
use App\Entity\User;
use App\Enum\UserRoleEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrganizationUserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class
            ])
            ->add('role', EnumType::class, [
                'class' => UserRoleEnum::class,
                'choices' => UserRoleEnum::getOrganizationRoles()
            ])
            ->add('default', CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrganizationUserRole::class
        ]);
    }
}
