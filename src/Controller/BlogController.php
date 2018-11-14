<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController {

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session) {

        $this->session = $session;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index() {

        return $this->render("blog/index.html.twig", ["posts" => $this->session->get("posts")]);
    }
    
    /**
     * @Route("/add", name="blog_add")
     */
    public function add() {
        
        $post = $this->session->get("posts");
        
        $post[uniqid()] = ["title" => "Random title ". rand(1, 500), "text" => "Random tex " . rand(1, 500)];
        
        $this->session->set("posts", $post);
        
        return $this->redirectToRoute("blog_index");
    }
    
    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id) {
        
        $posts = $this->session->get("posts");
        
        if (!$posts || !isset($posts[$id])) {
            
            throw new NotFoundHttpException("Post not found");
        }
        
        return $this->render("blog/post.html.twig", ["id" => $id, "post" => $posts[$id]]);
    }

}
