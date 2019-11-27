<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use App\Entity\BlogPost;

/**
 * Class BlogController
 *
 * @Route("/blog")
 *
 * @package App\Controller
 */
class BlogController extends AbstractController
{
    private const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello-world'
        ],
        [
            'id' => 2,
            'slug' => 'hello-world-2',
            'title' => 'Hello-world-2'
        ],
        [
            'id' => 3,
            'slug' => 'hello-world-3',
            'title' => 'Hello-world-3'
        ]
    ];


    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page":"\d+"})
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);
        return new JsonResponse(
            [
               'page' => $page,
               'limit' => $limit,
               'data' => array_map(function($item) {
                   return $this->generateUrl("blog_by_slug", ['id' => $item['slug']]);
               }, self::POSTS)
            ]
        );
    }

    /**
     * @Route("/{id}", name="blog_by_id", requirements={"id"="\d+"})
     *
     * @param $id
     * @return JsonResponse
     */
    public function post($id)
    {
        return new JsonResponse(
            self::POSTS[array_search($id, array_column(self::POSTS, "id"))]
        );
    }

    /**
     * @Route("/", name="blog_by_slug")
     *
     * @param $slug
     * @return JsonResponse
     */
    public function postBySlug($slug)
    {
        return new JsonResponse(
            self::POSTS[array_search($slug, array_column(self::POSTS, "slug"))]
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }
}