<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@test.com',
            'name' => 'Jean Saisrien',
            'password' => 'secret123#',
            'roles' => [User::ROLE_GOD]
        ],
        [
            'username' => 'braelyn_griffin',
            'email' => 'griffin@test.com',
            'name' => 'Braelyn Griffin',
            'password' => 'secret123#',
            'roles' => [User::ROLE_ADMIN]
        ],
        [
            'username' => 'dario_floyd',
            'email' => 'floyd@test.com',
            'name' => 'Dario Floyd',
            'password' => 'secret123#',
            'roles' => [User::ROLE_WRITER]
        ],
        [
            'username' => 'maren_george',
            'email' => 'george@test.com',
            'name' => 'Maren George',
            'password' => 'secret123#',
            'roles' => [User::ROLE_WRITER]
        ],
        [
            'username' => 'han_solo',
            'email' => 'han@test.com',
            'name' => 'Han Solo',
            'password' => 'secret123#',
            'roles' => [User::ROLE_EDITOR]
        ],
        [
            'username' => 'jedi_knight',
            'email' => 'jedi@test.com',
            'name' => 'Jedi Knight',
            'password' => 'secret123#',
            'roles' => [User::ROLE_COMMENTATOR]
        ],
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        for ($i=0; $i < 100; $i++) {
            $blogPost = new blogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisYear);
            $blogPost->setContent($this->faker->realText());
            $authorReference = $this->getRandomUsersReference($blogPost);

            $blogPost->setAuthor($authorReference);
            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost); // save data
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i=0; $i < 100; $i++) {
            for ($j=0; $j < rand(1, 10); $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomUsersReference($comment);

                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment); // save data
            }
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user, $userFixture['password']
            ));
            $user->setRoles($userFixture['roles']);

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user); // save data
        }

        $manager->flush();
    }

    protected function getRandomUsersReference($entity): User
    {
        $randomUser = self::USERS[rand(0, 5)];
        if ($entity instanceof BlogPost && !count(
            array_intersect(
                $randomUser['roles'],
                [User::ROLE_GOD, User::ROLE_ADMIN, User::ROLE_WRITER]
        ))) {
            return $this->getRandomUsersReference($entity);
        }

        if ($entity instanceof Comment && !count(
            array_intersect(
                $randomUser['roles'],
                [User::ROLE_GOD, User::ROLE_ADMIN, User::ROLE_WRITER, User::ROLE_COMMENTATOR]
            ))) {
            return $this->getRandomUsersReference($entity);
        }

        return $this->getReference('user_' . self::USERS[rand(0, 3)]['username']);
    }
}
