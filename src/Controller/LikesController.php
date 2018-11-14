<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/likes")
 */
class LikesController extends AbstractController {

    /**
     * @Route("/like/{id}", name="like_post")
     */
    public function likePost(MicroPost $microPost) {

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {

            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->likePost($currentUser);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(["count" => $microPost->getLikedBy()->count()]);
    }

    /**
     * @Route("/unlike/{id}", name="unlike_post")
     */
    public function unlikePost(MicroPost $microPost) {

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {

            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->getLikedBy()->removeElement($currentUser);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(["count" => $microPost->getLikedBy()->count()]);
    }

}
