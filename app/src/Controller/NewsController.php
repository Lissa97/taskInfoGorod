<?php

namespace App\Controller;

use \XMLWriter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class NewsController extends AbstractController
{
  /**
     * @Route("/news",  methods="GET")
     */
    public function getNews(Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page');
        $elementCount = (int) $request->query->get('elementCount');

        $news = $this->getDoctrine()
          ->getRepository(News::class)->getPaginateSort($page, $elementCount);

        $allNews = array();
        foreach($news as $n )
        {
            $allNews[] = $this -> getArrFromNews($n);
        }


        return new JsonResponse($allNews);
    }

  /**
     * @Route("/news/{slug}", methods="GET")
     */
    public function getNew($slug): JsonResponse
    {

        $news = $this->getDoctrine()
          ->getRepository(News::class)->getSlug($slug);

        if(sizeof($news)<1)
        {
          throw $this->createNotFoundException('Новость не найдена');
        }

        $new = $this -> getArrFromNews($news[0]);

        return new JsonResponse($new);
    }

      private function getArrFromNews($news){

        $cA = null;
        $uA = null;
        $pA = null;


        if($news->getCreatedAt())
          $cA = $news->getCreatedAt()  ->format('d.m.Y');

        if($news->getUpdatedAt())
          $uA = $news->getUpdatedAt()  ->format('d.m.Y');

        if($news->getPublishedAt())
          $pA = $news->getPublishedAt()->format('d.m.Y');


        return array(
          'id'               => $news->getId(),
          'title'            => $news->getTitle(),
          'slug'             => $news->getSlug(),
          'description'      => $news->getDescription(),
          'shortDescription' => $news->getShortDescription(),
          'createdAt'        => $cA,
          'updatedAt'        => $uA,
          'publishedAt'      => $pA,
          'isActive'         => $news->getIsActive(),
          'isHide'           => $news->getIsHide(),
          'Hits'             => $news->getHits()

        );
      }


}
