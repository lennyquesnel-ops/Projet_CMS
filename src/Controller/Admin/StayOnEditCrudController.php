<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class StayOnEditCrudController extends AbstractCrudController
{
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        if ($action === Action::EDIT) {
            $url = $this->container->get(AdminUrlGeneratorInterface::class)
                ->setAction(Action::EDIT)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

            return $this->redirect($url);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }
}
