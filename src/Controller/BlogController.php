<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository; // ici on reprend le namespace du repo et son nom
use App\Repository\CategoryRepository;

use App\Form\ArticleType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog/articles", name="blog")
     */
    public function articles(ArticleRepository $repo ) //ici, on lui indique qu'on a besoin de ce repo dans cette fonction
    {
        //$repo = $this->getDoctrine()->getRepository(Article::class);//on a besoin du repo pour aller chercher les données mais on lui a injecté cette dépendancce dans les () de la fonction.

        $articles = $repo->findAll(); // $articleS parce qu'on va en obtenir pls

        return $this->render('blog/articles.html.twig', [
            'controller_name' => 'BlogController',
            'articles'        => $articles
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
    * @Route("/blog/new", name="create") //on remonte cette fonction pour éviter les conflits avec celle d'après avec les id
    * @Route("/blog/{id}/edit", name="edit") //pour permettre de modifier le form avec la même fonction
    */
    public function form(Article $article = null, Request $request, ObjectManager $manager) {

      if(!$article) {
        $article = new Article();
      }

      // $form = $this->createFormBuilder($article)
      //             ->add('title', TextType::class, [
      //               'attr' => [
      //                 'placeholder' => "Titre de l'article",
      //                 'class'       => 'uk-input'
      //               ]
      //             ])
      //             ->add('content', TextareaType::class, [
      //               'attr' => [
      //                 'placeholder' => "Contenu de l'article",
      //                 'class'       => 'uk-textarea'
      //               ]
      //             ])
      //             ->add('image', TextType::class, [
      //               'attr' => [
      //                 'placeholder' => "Image de l'article",
      //                 'class'       => 'uk-input'
      //               ]
      //             ])
      //             ->getForm();

      $form = $this->createForm(ArticleType::class, $article); //on peut mettre ça à la place de la partie sup si on a créé un form

      $form->handleRequest($request);//on demande de gérer le formulaire en analysant les données remplies dans la requête

      if($form->isSubmitted() && $form->isValid()) {
        $data = $request->request->get('article');
           foreach($data['category'] as $category_id)
           {
               $rep = $this->getDoctrine()->getRepository(Category::class);
               $category = $rep->find($category_id);
               $article->addCategory($category);
           }
        

          $manager->persist($article);
          $manager->flush();

          return $this->redirectToRoute('show', ['id' => $article->getId()]);
      }

      return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'article'     => $article,
            'editMode'    => $article->getId() !== null
      ]);
    }

    /**
    * @Route("/blog/{id}", name="show") // route paramétrée avec l'id
    */
    public function show(Article $article, CategoryRepository $repo)//ArticleRepository $repo, $id: le paramconverter permet de chercher l'article qui correspond à l'id sans avoir à lui définier en + un repo et une id
    {
      // $repo = $this->getDoctrine()->getRepository(Article::class);

      //$article = $repo->find($id); plus besoin grâce au paramconverter

      $category = $repo->findAll();

      return $this->render('blog/show.html.twig', [
        'article'  => $article, //on lui passe la variable article pour qu'il aille chercher les données de $article
        'category' => $category
      ]);
    }


}
