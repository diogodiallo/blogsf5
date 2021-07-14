<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i=0; $i <= 3 ; $i++) { 
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());
                
            $manager->persist($category);

            for ($j=0; $j < mt_rand(4,6); $j++) { 
                $post = new Post();
                
                $content = "<p>" .join("</p><p>", $faker->paragraphs(5)). "</p>";

                $post->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);
    
                $manager->persist($post);

                for ($k=0; $k <= mt_rand(4,10) ; $k++) { 
                    $comment = new Comment();

                    $days = (new \DateTime())->diff($post->getCreatedAt())->days;

                    $comment->setAuthor($faker->name())
                            ->setContent($faker->paragraph(45))
                            ->setCreatedAt($faker->dateTimeBetween('-'. $days .' jours', '+1 years'))
                            ->setPost($post);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
