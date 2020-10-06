<?php
namespace App\EventListener;

use \XMLWriter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Controller\NewsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\News;
use App\Responce\SiteMapResponce;

class SiteMapReload extends AbstractController
{

  public function onKernelTerminate(TerminateEvent $event)
  {
    $rsponse = $event->getResponse();

    if ($rsponse instanceof  SiteMapResponce) {

      $news = $this->getDoctrine()
        ->getRepository(News::class)->getSort();

      $fileName = "SiteMap.xml";

      $s = new XMLWriter();
      $s->openURI($fileName);
      $s->startDocument();
      $s->startElement('urlset');
      $s->startAttribute('xmlns');
      $s->text("http://localhost/");
      $s->endAttribute();

      foreach($news as $n )
      {
        $s->startElement('url');

          $s->startElement('loc');
          $s->text("http://localhost/news/".$n->getSlug());
          $s->endElement();

          $s->startElement('lastmod');

          if($n->getUpdatedAt())
            $s->text($n->getUpdatedAt()->format('d.m.Y'));
          else
            $s->text($n->getCreatedAt()->format('d.m.Y'));

          $s->endElement();

        $s->endElement();
      }

      $s->endElement();
      $s->endDocument();
    }


  }





}
