<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog/articles", name="blog")
     */
    public function articles()
    {
        return $this->render('blog/articles.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
    * @Route("/", name="home")
    */

    public function home()
    {
      return $this->render('blog/home.html.twig');
    }

    /**
    * @Route("/blog/12", name="show")
    */
    public function show()
    {
      return $this->render('blog/show.html.twig');
    }
}
