<?php

namespace AppBundle\Controller;

use AppBundle\Utils\CrawlerHelper;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $crawlForm = $this->createFormBuilder()
            ->add('startCrawl', SubmitType::class, array('label' => 'Start Crawling'))
            ->getForm();

        $em = $this->get('doctrine.orm.entity_manager');
        $crawlForm->handleRequest($request);
        if ($crawlForm->isSubmitted()) {
            $crawler = new CrawlerHelper($em);
            $crawler->processMainPage();
        }

        return $this->render('AppBundle:Default:index.html.twig', [
            'form' => $crawlForm->createView()
        ]);
    }


    /**
     * @Route("/crawl", name="crawl")
     * @Template()
     * @throws \RuntimeException
     */
    public function crawlAction()
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        $crawlHelper = new CrawlerHelper($em);
        $crawlHelper->processPage();

        return $this->render('AppBundle:Default:crawl.html.twig');
    }


    /**
     * @Route("/search", name="search")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $default = [];

        $searchForm = $this->createFormBuilder($default)
            ->add('keyword', TextType::class, ['label' => 'Enter a Keyword To Search'])
            ->add('submit', SubmitType::class, ['label' => 'Search'])
            ->getForm();

        $finalPages = [];
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $keyword = $searchForm['keyword']->getData();

            $keywords = [];
            if ($keyword) {
                // explode by space
                $keywords = explode(' ', $keyword);
                $keywords = array_map(function ($v) {
                    return trim($v);
                }, $keywords);
            }

            if (preg_match('/(.*)\|(.*)/', $keyword, $matches)) {
                $keywords = [trim($matches[1]), trim($matches[2])];
            }

            $em = $this->get('doctrine.orm.entity_manager');

            $pages = [];
            foreach ($keywords as $k) {
                $page = $em->getRepository('AppBundle:CrawledPage')
                    ->createQueryBuilder('p')
                    ->select('p')
                    ->where('p.paragraph LIKE :keyword')
                    ->orWhere('p.title LIKE :title')
                    ->orWhere('p.url LIKE :url')
                    ->setParameter('keyword', '%' . $k . '%')
                    ->setParameter('title', '%' . $k . '%')
                    ->setParameter('url', $k)
                    ->addOrderBy('p.viewCount')
                    ->getQuery()
                    ->getResult();
                $pages[] = $page;
            }

            $newPages = call_user_func_array('array_merge', $pages);

            usort($newPages, function($a, $b) {
                if($a->getViewCount() === $b->getViewCount()){ return 0 ; }
                return ($a->getViewCount() < $b->getViewCount()) ? 1 : -1;
            });

            foreach ($newPages as $p) {
                if (! in_array($p, $finalPages)) {
                    $finalPages[] = $p;
                }
            }
        }

        return $this->render('AppBundle:Default:search.html.twig', [
            'form' => $searchForm->createView(),
            'pages' => $finalPages
        ]);
    }


    /**
     * @Route("/update_view/{id}", defaults={"id"=""}, name="update_view")
     * @param Request $request
     * @return JsonResponse
     */
    public function updateViewCount(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $id = $request->request->get('id');
        $status = 'fail';
        if ($id !== '') {
            $pageCrawled = $em
                ->getRepository('AppBundle:CrawledPage')
                ->find($id);

            if ($pageCrawled) {
                $count = $pageCrawled->getViewCount();
                $pageCrawled->setViewCount($count + 1);
            }
            $em->persist($pageCrawled);
            $em->flush();
            $status = 'Success';
        }
        return new JsonResponse($status);
    }

}
