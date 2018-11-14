<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController {

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index() {

        // Instancia del usuario conectado en la APP
        $currentUser = $this->getUser();

        // Comprobar si el usuario es anonymous o es un usuario de la APP
        if ($currentUser instanceof User) {

            // Si esta logeado buscamos los post de los usuarios que sigue
            $posts = $this->getDoctrine()->getRepository(MicroPost::class)->findPostByFollowing($currentUser->getFollowing());
            
            // Comprobar si hay post para mostrar, si no hay se genera un array de usuarios para seguir
            $usersToFollow = (count($posts) === 0) ? $this->getDoctrine()->getRepository(User::class)->findUsersToFollowExceptUser($this->getUser()) : [];
            
        } else {

            // Si no esta logeado (es el user anonymous) mostramos todos los post
            $posts = $this->getDoctrine()->getRepository(MicroPost::class)->findBy([], ["time" => "DESC"]);
        }
        
        // Hacer el render del template con los post y por defecto un array vacio con los usuarios para seguir ( ?? [] )
        return $this->render("micro-post/index.html.twig", ["posts" => $posts, "usersToFollow" => $usersToFollow ?? []]);
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')", message="Access denied")
     */
    public function add(Request $request) {

        $microPost = new MicroPost();

        $form = $this->createForm(MicroPostType::class, $microPost);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //$microPost->setText($form->get("text")->getData());
            //dump($form->get("text")->getData()); die;
            // definir la hora cuando se crea el psot
            //$microPost->setTime(new DateTime); // se utiliza el ORM\PrePersist()
            // Añadir la relacion user ~ post
            $microPost->setUser($this->getUser());

            // instancia del entityManager
            $em = $this->getDoctrine()->getManager();

            // persistir el objeto
            $em->persist($microPost);

            // actualizar la BBDD
            $em->flush();

            // Volver al Index
            return $this->redirectToRoute("micro_post_index");
        }

        return $this->render("micro-post/add.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * Con la anotacion Security comprobara el Voter que hemos creado para los MicroPost con la accion ('edit') y el objeto ('post')
     * @Security("is_granted('edit', post)", message="Access denied.")
     */
    public function edit(Request $request, MicroPost $post) {

        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("micro_post_index");
        }

        return $this->render("micro-post/add.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * Con la anotacion Security comprobara el Voter que hemos creado para los MicroPost con la accion ('delete') y el objeto ('post')
     * @Security("is_granted('delete', post)", message="Access denied.")
     */
    public function delete(MicroPost $post) {

        $em = $this->getDoctrine()->getManager();

        $em->remove($post);
        $em->flush();

        // añadir flashMessage, se utilzia el key => value, donde key sera para en el twig acceder el mensaje
        $this->addFlash("deleted", "Post was deleted");

        return $this->redirectToRoute("micro_post_index");
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     * @param User $userWithPost
     */
    public function userPosts(User $userWithPost) {

        // gracias al parameter converter, el parametro userWithPost es una entidad de la class User, symfony hace un fetch lazy y podemos conseguir todos los
        // post con el metodo getPost(), que estara en la class __CG__AppEntityUser, en al cache del proyecto.
        //return $this->render("micro-post/index.html.twig", ["posts" => $userWithPost->getPosts()]);
        // la manera tradicional seria utilizando el findBy
        return $this->render("micro-post/user-post.html.twig", [
                    "posts" => $this->getDoctrine()->getRepository(MicroPost::class)->findBy(["user" => $userWithPost], ["time" => "DESC"]),
                    "user" => $userWithPost
        ]);
    }

    /**
     * @Route("/{id}", name="micro_post_show")
     */
    public function post(MicroPost $post) {

        // ParamComverter: Symfony permite pasar como parametro un objeto, en este caso una entidad con un ID asociado y automaticamente,
        // hacer el find por el id, de esta manera en el return se devuelve la variable del parametro, si no encuentra ningun lanzara una excpetion.
        // Lo que hace basicamente el param converter es la funcion comentada abajo.
        //$post = $this->getDoctrine()->getRepository(MicroPost::class)->find($id);

        return $this->render("micro-post/post.html.twig", ["post" => $post]);
    }

}
