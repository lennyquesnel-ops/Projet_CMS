<?php

namespace App\Controller\Admin;

use App\Entity\PageBloc;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class PageBlocCrudController extends StayOnEditCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageBloc::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bloc de page')
            ->setEntityLabelInPlural('Blocs de page')
            ->setDefaultSort(['ordre' => 'ASC'])
            ->setEntityPermission('ROLE_USER');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('page', 'Page')
            ->onlyOnIndex();

        yield AssociationField::new('bloc', 'Bloc')
            ->autocomplete()
            ->setQueryBuilder(function (QueryBuilder $queryBuilder): QueryBuilder {
                $user = $this->getUser();

                if ($this->isGranted('ROLE_ADMIN')) {
                    return $queryBuilder;
                }

                if (!$user instanceof User) {
                    return $queryBuilder->andWhere('1 = 0');
                }

                return $queryBuilder
                    ->leftJoin('entity.pageBlocs', 'pageBlocFiltre')
                    ->leftJoin('pageBlocFiltre.page', 'pageFiltre')
                    ->leftJoin('pageFiltre.profilsAcces', 'profilAccesFiltre')
                    ->leftJoin('profilAccesFiltre.users', 'userFiltre')
                    ->andWhere('userFiltre = :utilisateurConnecte')
                    ->setParameter('utilisateurConnecte', $user)
                    ->distinct();
            })
            ->setRequired(true)
            ->setHelp('Choisis un bloc appartenant à une page autorisée.')
            ->setColumns(8);

        yield IntegerField::new('ordre', 'Ordre')
            ->setRequired(true)
            ->setFormTypeOption('invalid_message', 'L’ordre doit être un nombre entier.')
            ->setHelp('Plus le nombre est petit, plus le bloc apparaît haut dans la page.')
            ->setColumns(4);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        if (!$user instanceof User) {
            return $qb->andWhere('1 = 0');
        }

        return $qb
            ->leftJoin('entity.page', 'pageFiltre')
            ->leftJoin('pageFiltre.profilsAcces', 'profilAccesFiltre')
            ->leftJoin('profilAccesFiltre.users', 'userFiltre')
            ->andWhere('userFiltre = :utilisateurConnecte')
            ->setParameter('utilisateurConnecte', $user)
            ->distinct();
    }
}