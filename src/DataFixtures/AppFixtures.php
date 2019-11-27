<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $blogPost = new blogPost();
        $blogPost->setTitle('A first post');
        $blogPost->setPublished(new \DateTime('2019-11-27 11:00:00'));
        $blogPost->setContent('New post fixtures');
        $blogPost->setAuthor('Batman');
        $blogPost->setSlug('batman-post-source');

        $manager->persist($blogPost); // save data

        $blogPost = new blogPost();
        $blogPost->setTitle('A secondt post');
        $blogPost->setPublished(new \DateTime('2019-11-24 08:00:00'));
        $blogPost->setContent('New post');
        $blogPost->setAuthor('Batman');
        $blogPost->setSlug('batman-post-second');

        $manager->persist($blogPost); // save data

        $manager->flush();
    }
}
