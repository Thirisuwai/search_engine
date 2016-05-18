<?php


namespace AppBundle\Command;

use AppBundle\Entity\ResultHeader;
use AppBundle\Utils\CrawlerHelper;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class CrawlerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('crawler:crawl')
            ->setDescription('Start Crawling URL Lists');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $crawlHelper = new CrawlerHelper($entityManager);

        if ($crawlHelper->isFirstCrawling()) {
            $crawlHelper->truncateResultHeaderTable();
            $crawlHelper->truncateResultDetailTable();
        }

        $sourceList = $entityManager->getRepository('AppBundle:Source')
            ->findAll();

        $resultHeader = null;
        foreach ($sourceList as $s) {
            if ($s->getStatus() === 1) {
                $url = $s->getUrl();
                $client = new Client();
                $crawler = $client->request('GET', $url);
                $status = $client->getResponse()->getStatus();
                if ($status === 200) {
                    $resultHeader = new ResultHeader();
                    $resultHeader->setMainPageAddress($url)
                        ->setMainPageTitle(trim($crawler->filter('title')->text()))
                        ->setViewCount(0)
                        ->setStatus(1)
                        ->setHeaderParagraph($crawler->filter('meta[name=description]')->attr('content'));

                    $entityManager->persist($resultHeader);
                    $crawlHelper->processSubPage($resultHeader, $url);
                }
            }
        }

        $entityManager->flush();
        
        $io = new SymfonyStyle($input, $output);
        $io->comment('Quit the crawler with CONTROL-C.');
    }
}