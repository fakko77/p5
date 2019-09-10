<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType ;


class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Post::class);

        $posts = $repo->findAll();
        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'posts' => $posts,
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('forum/home.html.twig', [
            'title' => "hello",
            'age' => 31
        ]);
    }

    /**
     * @Route("/forum/new", name="forum_create")
     */

    public function create(Request $request , ObjectManager $manager ){
       $auth =  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($auth == true ){

            return $this->redirectToRoute('home');
        }
        $post = new Post();
      
       

        $form = $this ->createFormBuilder($post)
                      ->add('titre')
                      ->add('content',TextareaType::class)
                      ->getForm();
                      
                      $form->handleRequest($request);

                      if($form->isSubmitted() && $form->isValid() )
                        {
              
                          $post->setDatePost(new \DateTime());
                        $post->setUserCreator("avoir avec mentor");
                         $manager->persist($post);
                          $manager->flush();

                          return $this->redirectToRoute('home');

                         }
                        return $this->render('/forum/create.html.twig',['formNew' => $form->createView()
                            ]);

                    }

    /**
     * @Route("/forum/{id}", name="forum_show")
     */
    public function show($id){

        $repo = $this->getDoctrine()->getRepository(Post::class);
        $post = $repo->find($id);
        return $this->render('forum/show.html.twig',
    ['post'=> $post]);

    }

   


    /**
     * @Route("/user", name="forum_user")
     */
    public function user(){
        return $this->render('forum/user.html.twig');

    }
    
}
