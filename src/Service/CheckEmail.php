<?php

namespace App\Service;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CheckEmail extends AbstractController

{
    /*private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }*/



    public function Check_Email($mail)
    {
        $repo = $this->getDoctrine()->getRepository(Users::class);
        $posts = $repo->findBy(['mail' => $mail]);
        if ($posts != null) {
            return true;
        }
    }

    /*public function Check_EmailJson($mail)
    {
        $repo = $this->getDoctrine()->getRepository(Users::class);
        $posts = $repo->findBy(['mail' => $mail]);
        if ($posts != null) {
            return $this->json(['Etat' => 'good']);
        } else {
            return $this->json(['Etat' => 'bad']);
        }
    }*/
}
