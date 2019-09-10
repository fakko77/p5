<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Users;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;


class SecurityController extends AbstractController
{
   
   
     /**
     * @Route("/registration", name="forum_registration")
     */
    public function registration(Request $request , ObjectManager $manager , UserPasswordEncoderInterface $encoder ){
        $user = new Users();

        $form = $this->createFormBuilder($user)
                     ->add('username')
                     ->add('mail',EmailType::class,
                        ['constraints' => [new Email([
                            'checkMX' => true,

                        ])],] )
                     ->add('pwd',RepeatedType::class, [  
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => ['attr' => ['class' => 'password-field']],
                        'required' => true,
                        'first_options'  => ['label' => 'Password'],
                        'second_options' => ['label' => 'Repeat Password'],
                        'constraints' => [new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ])],
                        
                    ])
                  
                     ->getForm();


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){

            $user->setDateInscription(new \DateTime());
            $hash = $encoder->encodePassword($user , $user->getPwd()); 

            $user->setPwd($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('forum_login');
        }



        dump($user);
        return $this->render('forum/registration.html.twig',['formUser' => $form->createView()
        ]);

    }
     /**
     * @Route("/login", name="forum_login") 
     */
    public function login(){ 
            
                return $this->render('forum/login.html.twig');
               

    }

     /**
     * @Route("/logout", name="forum_logout") 
     */
    public function logout(){ 
            
      

}
}
