<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Post;


use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Article;class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1 ; $i <= 10 ; $i++ ){

            $post = new Post();
            $post->setTitre("titre post $i")
                 ->setContent("contenue de l'article $i")
                 ->setImage("")
                 ->setCreatAt(new \DateTime())
                 ->setCreatBy("utilsateur n°$i");

                 $manager->persist($post);


        }

        $manager->flush();
    }
}
