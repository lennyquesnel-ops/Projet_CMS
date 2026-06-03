<?php

namespace App\Security\Voter;

use App\Entity\Bloc;
use App\Entity\Page;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BlocVoter extends Voter
{
    public const EDIT = 'BLOC_EDIT';
    public const DELETE = 'BLOC_DELETE';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Bloc;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Bloc $bloc */
        $bloc = $subject;

        if ($attribute === self::DELETE && $bloc->isUsedInPage()) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $this->userCanManageBloc($user, $bloc);
    }

    private function userCanManageBloc(User $user, Bloc $bloc): bool
    {
        if ($bloc->getPageBlocs()->isEmpty()) {
            return false;
        }

        foreach ($bloc->getPageBlocs() as $pageBloc) {
            $page = $pageBloc->getPage();

            if (!$page instanceof Page) {
                return false;
            }

            if (!$this->userCanEditPage($user, $page)) {
                return false;
            }
        }

        return true;
    }

    private function userCanEditPage(User $user, Page $page): bool
    {
        foreach ($user->getProfilsAcces() as $profilAcces) {
            foreach ($profilAcces->getPages() as $pageAutorisee) {
                if ($pageAutorisee->getId() === $page->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}