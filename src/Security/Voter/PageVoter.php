<?php

namespace App\Security\Voter;

use App\Entity\Page;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PageVoter extends Voter
{
    public const EDIT = 'PAGE_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Page;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Page $page */
        $page = $subject;

        return $this->userCanEditPage($user, $page);
    }

    private function userCanEditPage(User $user, Page $page): bool
    {
        $pageId = $page->getId();

        if ($pageId === null) {
            return false;
        }

        foreach ($user->getProfilsAcces() as $profilAcces) {
            foreach ($profilAcces->getPages() as $pageAutorisee) {
                if ($pageAutorisee->getId() === $pageId) {
                    return true;
                }
            }
        }

        return false;
    }
}