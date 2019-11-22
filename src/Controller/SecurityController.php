<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Users;
use  App\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Response;
use App\Service\CheckEmail;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends AbstractController
{


    /**
     * @Route("/registration", name="forum_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new Users();
        $repo = $this->getDoctrine()->getRepository(Role::class);
        $role = $repo->find(2);
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('mail', EmailType::class)
            ->add('pwd', RepeatedType::class, ['type' => PasswordType::class, 'invalid_message' => 'Les champs du mot de passe doivent correspondre..', 'options' => ['attr' => ['class' => 'password-field']], 'required' => true, 'first_options'  => ['label' => 'Password'], 'second_options' => ['label' => 'Repeat Password'], 'constraints' => [new Length(['min' => 6, 'minMessage' => 'Votre mot de passe doit être au moins de {{ limit }} characters', 'max' => 4096])]])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setDateInscription(new \DateTime());
            $hash = $encoder->encodePassword($user, $user->getPwd());
            $user->setPwd($hash);
            $user->setRole($role);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('forum_login');
        }
        //dump($user);
        return $this->render('forum/registration.html.twig', ['formUser' => $form->createView()]);
    }
    /**
     * @Route("/login", name="forum_login") 
     */
    public function login()
    {
        return $this->render('forum/login.html.twig');
    }

    /**
     * @Route("/logout", name="forum_logout") 
     */
    public function logout()
    { }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repo->findBy(['valid' => 'non']);
        $auth =  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($auth == true) {
            return $this->redirectToRoute('home');
        }
        if ($this->getUser()) {
            $username = $this->getUser();
        } else {
            return $this->redirectToRoute('home');
        }
        $userRole =  $username->getRole();
        if ($username->getRole() != "1") {
            return $this->redirectToRoute('home');
        } else { }
        return $this->render('forum/admin.html.twig', ['title' => "hello", 'age' => 31, 'role' => $userRole, 'posts' => $posts,]);
    }

    /**
     * @Route("forum/compte/{id}", name="compte_info") 
     */
    public function info($id, Request $request)
    {
        $user = $this->getUser($id);
        $author = $this->getUser()->getUsername();
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $repo2 = $this->getDoctrine()->getRepository(Comment::class);
        $post = $repo->findBy(['userCreator' => $author]);
        $comment = $repo2->findBy(['author' => $author]);
        $currentRoute = $request->attributes->get('_route');
        return $this->render('forum/compte.html.twig', ['user' => $user, 'posts' => $post, 'comments' => $comment, 'val' => $currentRoute]);
    }

    /**
     * @Route("forum/compte/modification/{user}", name="compte_modification") 
     */
    public function modification(Users $user, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $username = $this->getUser();
        $nom = $user->getUsername();
        /*  $repo = $this->getDoctrine()->getRepository(Users::class);
        $role = $repo->find(2);*/
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('mail', EmailType::class, ['constraints' => [new Email(['checkMX' => true])],])
            ->add('pwd', RepeatedType::class, ['type' => PasswordType::class, 'invalid_message' => 'Les champs du mot de passe doivent correspondre.', 'options' => ['attr' => ['class' => 'password-field']], 'required' => true, 'first_options'  => ['label' => 'Password'], 'second_options' => ['label' => 'Repeat Password'], 'constraints' => [new Length(['min' => 6, 'minMessage' => 'Votre mot de passe doit être au moins de {{ limit }} characters', 'max' => 4096])]])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $user->setDateInscription(new \DateTime());
            $hash = $encoder->encodePassword($user, $user->getPwd());
            $user->setPwd($hash);
            // $user->setRole($role);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('compte_info', ['id' => $username->getId()]);
        }

        return $this->render('forum/registration.html.twig', ['formUser' => $form->createView()]);
    }


    /**
     * @Route("recuperation", name="recuperation") 
     */
    public function recuperation(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email')
            ->getForm();
        $form->handleRequest($request);

        return $this->render('forum/recuperation.html.twig', ['formUser' => $form->createView()]);
    }








    /*$form = $this->createFormBuilder()
            ->add('email')
            ->getForm();
        $form->handleRequest($request);*/





    ///  $email = $mail_val;
    /*  $serializer = SerializerBuilder::create()->build();
        $reponse_possitif = $serializer->serialize("oui", 'json');
        $reponse_negatif = $serializer->serialize("non", 'json');
        /*  $reponse_possitif =  $this->json(['valeur' => 'oui']);
        $reponse_negatif =  $this->json(['valeur' => 'non']);*/
    /* if ($email != null) {
            if ($mail->Check_Email($email) == true) {

                return $this->render('forum\recuperation.html.twig', ["reponse" => $reponse_possitif, 'formUser' => $form->createView(), 'email' => $mail_val]);
            } else {

                return $this->render('forum\recuperation.html.twig', ["reponse" => $reponse_negatif, 'formUser' => $form->createView(), 'email' => $mail_val]);
            }
        }
        return $this->render('forum\recuperation.html.twig', ["reponse" => "non", 'formUser' => $form->createView(), 'email' => "e"]);*/

    /*  if ($mail->Check_Email($email) == true) {
            $response = $mail->Check_EmailJson($email);
            $serializer = SerializerBuilder::create()->build();
            $jsonObject = $serializer->serialize($response, 'json');

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('p5symfony@gmail.com')
                ->setSubject("réinitialisation de mot de passe")
                ->setTo($email)
                ->setBody(
                    "testets"
                );
            $mailer->send($message);

            return $this->render('forum\recuperation.html.twig', ["reponse" => $jsonObject]);
        }
        $response = $mail->Check_EmailJson($email);
        $serializer = SerializerBuilder::create()->build();
        $jsonObject = $serializer->serialize($response, 'json');

        return $this->render('forum\recuperation.html.twig', ["reponse" => $response]);
    }*/
}
