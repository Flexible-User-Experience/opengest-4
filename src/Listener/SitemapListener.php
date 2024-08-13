<?php

namespace App\Listener;

use App\Menu\FrontendMenuBuilder;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Knp\Menu\MenuItem;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SitemapListener.
 */
class SitemapListener implements EventSubscriberInterface
{
    private FrontendMenuBuilder $menuBuilder;

    /**
     * Methods.
     */
    public function __construct(FrontendMenuBuilder $menuItem)
    {
        $this->menuBuilder = $menuItem;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populateSitemap',
        ];
    }

    /**
     * @throws Exception
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || 'default' == $section) {
            $sitemap = $this->menuBuilder->createSitemapMenu();
            /** @var MenuItem $item */
            foreach ($sitemap->getIterator() as $item) {
                if ($item->getExtra('updated_at')) {
                    $event
                        ->getUrlContainer()
                        ->addUrl($this->makeUrlConcrete($item->getName(), 1, $item->getExtra('updated_at')), 'default');
                } else {
                    $event
                        ->getUrlContainer()
                        ->addUrl($this->makeUrlConcrete($item->getName()), 'default');
                }
                if (count($item->getChildren()) > 0) {
                    /** @var MenuItem $child */
                    foreach ($item->getChildren() as $child) {
                        if ($child->getExtra('updated_at')) {
                            $event
                                ->getUrlContainer()
                                ->addUrl($this->makeUrlConcrete($child->getName(), 0.8, $child->getExtra('updated_at')), 'default');
                        } else {
                            $event
                                ->getUrlContainer()
                                ->addUrl($this->makeUrlConcrete($child->getName(), 0.8), 'default');
                        }
                        if (count($child->getChildren()) > 0) {
                            /** @var MenuItem $grandchild */
                            foreach ($child->getChildren() as $grandchild) {
                                if ($grandchild->getExtra('updated_at')) {
                                    $event
                                        ->getUrlContainer()
                                        ->addUrl($this->makeUrlConcrete($grandchild->getName(), 0.5, $grandchild->getExtra('updated_at')), 'default');
                                } else {
                                    $event
                                        ->getUrlContainer()
                                        ->addUrl($this->makeUrlConcrete($grandchild->getName(), 0.5), 'default');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $url
     * @param int    $priority
     *
     * @return UrlConcrete
     *
     * @throws Exception
     */
    private function makeUrlConcrete($url, $priority = 1, ?DateTimeInterface $date = null): UrlConcrete
    {
        return new UrlConcrete(
            $url,
            null === $date ? new DateTimeImmutable() : $date,
            UrlConcrete::CHANGEFREQ_WEEKLY,
            $priority
        );
    }
}
