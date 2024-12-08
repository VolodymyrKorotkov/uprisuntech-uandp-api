<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\GuideBook;
use App\Enum\UserRoleEnum;
use App\Form\Field\TranslationField;
use App\Form\SubGuideType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[IsGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)]
final class GuideBookCRUDController extends AppAbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return GuideBook::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.parent is null');
        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('Title', 'title')->hideOnForm(),
            TranslationField::new('translations', 'Translations', [
                'title' => [
                    'field_type' => TextType::class,
                    'required' => true,
                ]
            ])->setRequired(true)->hideOnIndex(),
            BooleanField::new('enable'),
            CollectionField::new('guideBooks', 'Inhalt')
                ->setEntryIsComplex(true)
                ->setEntryType(SubGuideType::class)
                ->setFormTypeOption('allow_delete', true)
                ->setFormTypeOption('allow_add', true)
                ->formatValue(function ($value) {
                    return implode('; ', array_map(fn ($item) =>  $item->getTitle(),$value->toArray()));
                })
        ];
    }
}
