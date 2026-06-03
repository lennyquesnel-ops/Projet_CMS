<?php

namespace App\EventSubscriber;

use App\Entity\Bloc;
use App\Entity\ElementMenu;
use App\Entity\Media;
use App\Entity\Menu;
use App\Entity\Page;
use App\Entity\PageBloc;
use App\Entity\Parametre;
use App\Repository\PageRepository;
use App\Service\StaticPageGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StaticCacheAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var array<class-string>
     */
    private const CACHEABLE_ENTITIES = [
        Page::class,
        Bloc::class,
        PageBloc::class,
        Menu::class,
        ElementMenu::class,
        Parametre::class,
        Media::class,
    ];

    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly StaticPageGenerator $staticPageGenerator,
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'regenerateStaticCache',
            AfterEntityUpdatedEvent::class => 'regenerateStaticCache',
            AfterEntityDeletedEvent::class => 'regenerateStaticCache',
        ];
    }

    public function regenerateStaticCache(object $event): void
    {
        if (!method_exists($event, 'getEntityInstance')) {
            return;
        }

        $entity = $event->getEntityInstance();

        if (!$this->shouldRegenerateFor($entity)) {
            return;
        }

        try {
            $pages = $this->pageRepository->findAllWithBlocs();

            $this->staticPageGenerator->generateAll($pages);
        } catch (\Throwable $exception) {
            $this->logger->error('Erreur pendant la génération du cache des pages.', [
                'exception' => $exception,
                'entity' => $entity::class,
            ]);
        }
    }

    private function shouldRegenerateFor(object $entity): bool
    {
        foreach (self::CACHEABLE_ENTITIES as $className) {
            if ($entity instanceof $className) {
                return true;
            }
        }

        return false;
    }
}