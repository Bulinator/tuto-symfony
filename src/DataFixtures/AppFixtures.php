<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        // get instance of user
        $user = $this->getReference('user_admin');

        $blogPost = new blogPost();
        $blogPost->setTitle('A first post');
        $blogPost->setPublished(new \DateTime('2019-11-27 11:00:00'));
        $blogPost->setContent('New post fixtures');
        $blogPost->setAuthor($user);
        $blogPost->setSlug('batman-post-source');

        $manager->persist($blogPost); // save data

        $blogPost = new blogPost();
        $blogPost->setTitle('A second post');
        $blogPost->setPublished(new \DateTime('2019-11-24 08:00:00'));
        $blogPost->setContent('New post');
        $blogPost->setAuthor($user);
        $blogPost->setSlug('batman-post-second');

        $manager->persist($blogPost); // save data

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {

    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@blog.com');
        $user->setName('Batman Wayne');

        $user->setPassword('secret1234#');

        $this->addReference('user_admin', $user);

        $manager->persist($user); // save data

        $manager->flush();
    }
}
