<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;


use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Article;
class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    
    { 
        $faker = \Faker\Factory::create('fr_FR');
       
        for($i = 1 ; $i <= 3; $i++ ){
            $category = new Category();
            $category->setTitle($faker->sentence())
                     -> setDescription($faker->paragraph());

                    $manager->persist($category);
        }



                    for($j = 1 ; $j <= mt_rand(4,6) ; $j++ ){

                   
                    $contents = '<p>' .join($faker->paragraphs(5), '</p><p>').'</p>';
                   
                        $post = new Post();
                        $post->setTitre($faker->sentence())
                             ->setContent($contents)
                             ->setDatePost($faker->dateTimeBetween('-6 months'))
                             ->setUserCreator("utilsateur n°$i")
                             ->setCategory($category);
            
                             $manager->persist($post);
                            }


                    for($k = 1; $k <= mt_rand(4, 10); $k++){

                        $comment = new Comment();
                        $content = '<p>' .join($faker->paragraphs(5), '</p><p>').'</p>';
                    
                        $days = (new \DateTime())->diff($post->getDatePost())->days;
                       
                   
                            $comment->setAuthor($faker->name)
                                    ->setContent($content)
                                    ->setCreatedAt($faker->dateTimeBetween('-'.$days.'days'))
                                    ->setArticle($post);

                            $manager->persist($comment);

                    }
            
                  
                    $manager->flush();
                    
        }
       
      
    }
