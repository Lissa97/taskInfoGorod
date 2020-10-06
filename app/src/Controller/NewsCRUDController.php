<?php

namespace App\Controller;

use \Datetime;
use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Responce\SiteMapResponce;

class NewsCRUDController extends AbstractController
{
  /**
   * @Route("/news", methods="POST")
   */
  public function setNew(Request $request): SiteMapResponce
  {
      $content = json_decode($request->getContent(), true);

      if (! $content
          || !array_key_exists("title", $content)
          || !array_key_exists("description", $content)
          || !array_key_exists("shortDescription", $content)
          || !array_key_exists("publishedAt", $content)
          || !array_key_exists("isActive", $content)
          || !array_key_exists("isHide", $content)

        ){
          throw new \Exception('Не верный формат данных!');
      }

      $news = new News();

      $news->setTitle($content['title']);
      $news->setSlug($content['title']);
      $news->setDescription($content['description']);
      $news->setShortDescription($content['shortDescription']);
      $news->setCreatedAt(new DateTime());
      $news->setPublishedAt(new DateTime($content['publishedAt']));
      $news->setIsActive($content['isActive']);
      $news->setIsHide($content['isHide']);
      $news->setHits(0);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($news);
      $entityManager->flush();

      $news->setSlug($this->genSlug($content['title'], $news->getId()));
      $entityManager->persist($news);
      $entityManager->flush();

      return new SiteMapResponce($news->getId());
  }

  /**
   * @Route("/news", methods="PUT")
   */
  public function updateNew(Request $request): SiteMapResponce
  {
      $content = json_decode($request->getContent(), true);

      if (! $content  || !array_key_exists("id", $content)    ) {
          throw new \Exception('Не верный формат данных!');
      }


      $news = $this->getDoctrine()
        ->getRepository(News::class)->find($content['id']);

      if(!$news){
        throw $this->createNotFoundException('Новость не найдена');
      }

      if(!array_key_exists("title", $content)){
        $news->setTitle($content['title']);
        $news->setSlug($this->genSlug($content['title'], $news->getId()));
      }

      if(!array_key_exists("description", $content))
        $news->setDescription($content['description']);

      if(!array_key_exists("shortDescription", $content))
        $news->setShortDescription($content['shortDescription']);

      $news->setUpdatedAt(new DateTime());

      if(!array_key_exists("publishedAt", $content))
        $news->setPublishedAt(new DateTime($content['publishedAt']));

      if(!array_key_exists("isActive", $content))
        $news->setIsActive($content['isActive']);

      if(!array_key_exists("isHide", $content))
        $news->setIsHide($content['isHide']);

      if(!array_key_exists("hits", $content))
        $news->setHits($content['hits']);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($news);
      $entityManager->flush();

      return new SiteMapResponce($news->getId());
  }

  /**
   * @Route("/news/{id}", methods="DELETE")
   */
  public function deleteNew($id): SiteMapResponce
  {

      $news = $this->getDoctrine()
        ->getRepository(News::class)->find($id);

      if(!$news){
        throw $this->createNotFoundException('Новость не найдена');
      }

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($news);
      $entityManager->flush();

      return new SiteMapResponce($id);
  }

  private function genSlug($title, $id){
    return $id.".".$title;
  }


  /**
       * @Route("/test", methods="GET")
       */
      public function testNew(): Response
      {
          $request = Request::create(
            '/news',
            'PUT',
            ['id' => 1],
            [],
            [],
            [],
            '{"id": 7, "title":"rryrtyrtyutru3","description":"tyutyutyu",
              "shortDescription":"t", "publishedAt":"01.09.2020",
               "isActive": true, "isHide": false, "hits": 2
              }'
          );

          return $this->updateNew($request);

      }



}
