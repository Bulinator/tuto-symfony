<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
//    private const POSTS = [
//        [
//            'id' => 1,
//            'slug' => 'hello-world',
//            'title' => 'Hello-world'
//        ],
//        [
//            'id' => 2,
//            'slug' => 'hello-world-2',
//            'title' => 'Hello-world-2'
//        ],
//        [
//            'id' => 3,
//            'slug' => 'hello-world-3',
//            'title' => 'Hello-world-3'
//        ]
//    ];


    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page":"\d+"})
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return new JsonResponse(
            [
               'page' => $page,
               'limit' => $limit,
               'data' => array_map(function($item) {
                   return $this->generateUrl("blog_by_slug", ['id' => $item->getSlug()]);
               }, $items)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
     * @ParamConverter("post", class="App:BlogPost")
     *
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function post($post) // can also declare BlogPost without annotation
    {
        return $this->json($post);
//      it's the same as doing on find($id) on repository
//        return new JsonResponse(
//            $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
//        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     * The below annotation is not required when $post type hinted with BlogPost
     * and route parameter name matches any fields on the blogPost entity
     *
     * @ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
     *
     * @param $post
     * @return JsonResponse
     */
    public function postBySlug($post)
    {
        return $this->json($post);
//      it's the same as doing on findBySlug
//        return new JsonResponse(
//            $this->getDoctrine()->getRepository(BlogPost::class)->findBySlug(['slug' => $slug])
//        );
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