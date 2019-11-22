<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Category;
use App\Repository\PostRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Msg;
use App\Service\CheckEmail;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum-{cat}", name="forum")
     */
    public function index($cat = null, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $repo2 =  $this->getDoctrine()->getRepository(Category::class);
        $form = $this->createFormBuilder()
            ->add('category', EntityType::class, ['class' => Category::class, 'choice_label' => 'title'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $val = $form->get('category')->getData();
            $val = $val->getTitle();
            return $this->redirectToRoute('forum', ['cat' => $val]);
        }
        $idcategory = $repo2->findOneBy(['title' => $cat]);
        $posts = $repo->findBy(['valid' => 'oui']);
        $postfiltre = $repo->findBy(['valid' => 'oui', 'category' => $idcategory]);

        if ($cat != null) {
            return $this->render('forum/index.html.twig', ['controller_name' => 'ForumController', 'filtre' => $form->createView(), 'posts' => $postfiltre,]);
        } else {
            return $this->render('forum/index.html.twig', ['controller_name' => 'ForumController', 'filtre' => $form->createView(), 'posts' => $posts,]);
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {

        return $this->render('/forum/home.html.twig');
    }


    /**
     * @Route("/forum/new", name="forum_create")
     * @Route("/forum/{id}/edit", name="forum_edit")
     */

    public function form(Post $post = null, Request $request, ObjectManager $manager)
    {
        $username = $this->getUser();
        $auth =  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($auth == true) {
            return $this->redirectToRoute('home');
        }
        if (!$post) {
            $post = new Post();
        } elseif ($post->getUserCreator() !== $username->getUsername()) {
            return $this->redirectToRoute('home');
        }
        $form = $this->createFormBuilder($post)
            ->add('titre')
            ->add('content', TextareaType::class, array('attr' => array('class' => 'ckeditor')))
            ->add('category', EntityType::class, ['class' => Category::class, 'choice_label' => 'title'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$post->getId()) {
                $post->setDatePost(new \DateTime());
            }
            $post->setValid("non");
            $post->setUserCreator($username);
            $post->setUsers($username);
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('/forum/create.html.twig', ['formNew' => $form->createView()]);
    }

    /**
     * @Route("/forum/{id}", name="forum_show")
     */
    public function show($id, Post $post, Request $request, ObjectManager $manager)
    {



        if ($this->getUser()) {
            $username = $this->getUser()->getUsername();
            $userId = $this->getUser();
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $post = $repo->find($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setArticle($post)
                ->setAuthor($username)
                ->setUser($userId);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('forum_show', ['id' => $post->getId()]);
        }
        $serializer = SerializerBuilder::create()->build();
        $jsonObject = $serializer->serialize($post, 'json');

        return $this->render('forum/show.html.twig', ['post' => $post, 'commentForm' => $form->createView(), 'json' => $jsonObject]);
    }





    /**
     * @Route("forum/delete/post/{id}", name="delete_post" )
     */
    public function deletePost($id, ObjectManager $manager)
    {
        $username = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $post = $repository->find($id);
        $manager->remove($post);
        $manager->flush();
        return $this->redirectToRoute('compte_info', ['id' => $username->getId()]);
    }

    /**
     * @Route("forum/delete/com/{id}/{route}/{articleid}", name="delete_com" )
     */
    public function deleteCom($id, $route = null, $articleid = null, ObjectManager $manager)
    {
        $username = $this->getUser();
        $data = $this->getDoctrine()->getRepository(Comment::class);
        $com = $data->find($id);
        $manager->remove($com);
        $manager->flush();
        if ($route == "compte_info") {
            return $this->redirectToRoute('compte_info', ['id' => $username->getId()]);
        } else if ($articleid != null) {
            return $this->redirectToRoute('forum_show', ['id' => $articleid]);
        } else { }
    }

    /**
     * @Route("forum/{id}/valid", name="valid" )
     */
    public function valid($id,  ObjectManager $manager)
    {
        $data = $this->getDoctrine()->getRepository(Post::class);
        $post = $data->find($id);
        $post->setValid("oui");
        $manager->persist($post);
        $manager->flush();
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/json/{email}", name="jsonAffichage")
     */

    public function jsonAffichage(CheckEmail $mailCheck, $email = null, \Swift_Mailer $mailer)
    {

        if ($mailCheck->Check_Email($email) == true) {
            return $this->json(['val' => 'oui']);


            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('p5symfony@gmail.com')
                ->setSubject("réinitialisation de mot de passe")
                ->setTo($email)
                ->setBody(
                    "testets"
                );
            $mailer->send($message);
        }

        return $this->json(['val' => 'non']);
    }
}
