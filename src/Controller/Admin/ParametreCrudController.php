<?php

namespace App\Controller\Admin;

use App\Entity\Parametre;
use App\Form\ParametreValeurType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class ParametreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Parametre::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Paramètre')
            ->setEntityLabelInPlural('Paramètres')
            ->setDefaultSort(['id' => 'ASC'])
            ->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('code_parametre', 'Code paramètre')
            ->setRequired(true)
            ->setHelp(
                'Exemples : '
                . Parametre::CODE_LOGO_SITE . ', '
                . Parametre::CODE_SITE_URL . ', '
                . Parametre::CODE_THEME_CSS . ', '
                . Parametre::CODE_CUSTOM_CSS . '.'
            );

        yield TextareaField::new('valeur_parametre', 'Valeur paramètre')
            ->setRequired(false)
            ->setFormType(ParametreValeurType::class)
            ->setFormTypeOption('media_browser_url', $this->generateUrl('admin_media_browser'))
            ->setHelp(
                'Pour le logo, choisis une image. '
                . 'Pour les CSS, ce champ sera rempli automatiquement avec le nom du fichier uploadé.'
            );

        yield Field::new('fichierCss', 'Fichier CSS')
            ->onlyOnForms()
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'required' => false,
                'constraints' => [
                    new FileConstraint([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'text/css',
                            'text/plain',
                            'text/x-css',
                            'application/octet-stream',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être un fichier CSS.',
                    ]),
                ],
            ])
            ->setHelp(
                'À utiliser avec le code "'
                . Parametre::CODE_THEME_CSS
                . '" pour le thème, ou "'
                . Parametre::CODE_CUSTOM_CSS
                . '" pour le CSS personnalisé.'
            );

        yield TextareaField::new('description', 'Description')
            ->hideOnIndex();
    }

    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if ($entityInstance instanceof Parametre) {
            $this->handleCssUpload($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if ($entityInstance instanceof Parametre) {
            $this->handleCssUpload($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleCssUpload(Parametre $parametre): void
    {
        $fichierCss = $parametre->getFichierCss();

        if (!$fichierCss instanceof UploadedFile) {
            return;
        }

        if (!in_array($parametre->getCodeParametre(), [
            Parametre::CODE_THEME_CSS,
            Parametre::CODE_CUSTOM_CSS,
        ], true)) {
            $this->addFlash(
                'warning',
                'Le fichier CSS est ignoré car le code paramètre doit être "'
                . Parametre::CODE_THEME_CSS
                . '" ou "'
                . Parametre::CODE_CUSTOM_CSS
                . '".'
            );

            return;
        }

        $extension = strtolower($fichierCss->getClientOriginalExtension());

        if ($extension !== 'css') {
            $this->addFlash('danger', 'Le fichier doit avoir l’extension .css.');

            return;
        }

        $nomOriginal = pathinfo($fichierCss->getClientOriginalName(), PATHINFO_FILENAME);
        $nomNettoye = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($nomOriginal));
        $nouveauNom = $parametre->getCodeParametre() . '-' . $nomNettoye . '-' . uniqid() . '.css';

        $fichierCss->move(
            $this->getParameter('app.uploads.styles_dir'),
            $nouveauNom
        );

        $parametre->setValeurParametre($nouveauNom);
        $parametre->setFichierCss(null);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un paramètre')
            );
    }
}