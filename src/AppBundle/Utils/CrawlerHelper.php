<?php

namespace AppBundle\Utils;

use AppBundle\Entity\CrawledPage;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\VarDumper\VarDumper;


class CrawlerHelper
{
    const MAX_CRAWL_LIMIT = 500;

    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;


    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }


    public function processPage()
    {

        if ($this->isFirstCrawling()) {
            $this->truncateCrawledPageTable();
        }

        $sourceList = $this->entityManager->getRepository('AppBundle:Source')
            ->findAll();

        foreach ($sourceList as $s) {
            if ($s->getStatus() === 1) {
                $this->crawlPage($s->getUrl());
            }
        }

    }


    public function crawlPage($url)
    {
        if (!$this->isExistingUrl($url)) {
            $client = new Client();
            /** @var Crawler $crawler */
            $crawler = $client->request('GET', $url);
            $crawlContent = $crawler->filter('meta[name=description]');
            $crawlTitle = $crawler->filter('title');
            $content = $title = '';
            if ($crawlContent->count() > 0) {
                $content = $crawlContent->attr('content');
            } else {
                $crawlKeyword = $crawler->filter('meta[name=keywords]');
                if ($crawlKeyword->getNode('content')) {
                    $content = $crawlContent->attr('content');
                }
            }

            if ($crawlTitle->count() > 0) {
                $title = $crawlTitle->text();
            }

            $resultHeader = new CrawledPage();
            $resultHeader->setUrl($url)
                ->setTitle(trim($title))
                ->setViewCount(0)
                ->setStatus(1)
                ->setParagraph($content)
                ->setCreatedAt(new \DateTime());

            $this->entityManager->persist($resultHeader);
            $this->entityManager->flush();

            $subUrls = [];
            $links = $crawler->filter('a[href]')->links();
            foreach ($links as $l) {
                $subUrls[] = $l->getUri();
            }
            array_unique($subUrls);

            $count = 0;
            if (count($subUrls) > 0) {
                foreach ($subUrls as $u) {
                    if ($this->isUrlCrawlable($u) && self::MAX_CRAWL_LIMIT >= $count) {
                        $this->crawlPage($u);
                    }
                    $count++;
                }
            }
        }
    }


    public function isFirstCrawling()
    {
        if ($this->isCrawledPageTableEmpty()) {
            return true;
        }
        return false;
    }


    /**
     * Check whether crawledPage table is empty
     * @return bool
     */
    public function isCrawledPageTableEmpty()
    {
        $resultHeader = $this->entityManager->getRepository('AppBundle:CrawledPage')
            ->findAll();

        return $resultHeader ? true : false;
    }


    public function truncateCrawledPageTable()
    {
        $resultHeaders = $this->entityManager->getRepository('AppBundle:CrawledPage')
            ->findAll();
        foreach ($resultHeaders as $h) {
            $this->entityManager->remove($h);
        }
        $this->entityManager->flush();
    }


    /**
     * This method check whether the url
     * @param $url
     * @return bool
     */
    public function isExistingUrl($url)
    {
        $findUrl = $this->entityManager->getRepository('AppBundle:CrawledPage')
            ->findOneBy(['url' => $url]);

        return $findUrl ? true : false;
    }


    /**
     * checks if url can be crawled or not
     * @param string $url
     * @return boolean
     */
    public function isUrlCrawlable($url) {
        if (empty($url)) {
            return false;
        }

        $stopLinks = array(//returned deadlinks
            '@^javascript\:void\(0\)$@',
            '@^#.*@',
        );

        foreach ($stopLinks as $pattern) {
            if (preg_match($pattern, $url)) {
                return false;
            }
        }
        return true;
    }

}