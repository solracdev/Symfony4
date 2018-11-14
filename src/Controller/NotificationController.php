<?php

namespace App\Controller;

use App\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends AbstractController {

    /**
     * @route("/unread-count", name="notification_unread")
     */
    public function unreadCount() {

        return new JsonResponse([
            "count" => $this->getDoctrine()->getRepository(Notification::class)->findUnseenByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/all", name="unseen_notifications")
     */
    public function notifications() {

        return $this->render("notification/notifications.html.twig", [
                    "notifications" => $this->getDoctrine()->getRepository(Notification::class)->findBy([
                        "seen" => false,
                        "user" => $this->getUser()
                    ])
        ]);
    }

    /**
     * @Route("/check/{id}", name="check_notification")
     */
    public function checkNotification(Notification $notification) {
        
        // Marcar la notificacion como vista / leida
        $notification->setSeen(true);
        
        // Actualizar la BBDD
        $this->getDoctrine()->getManager()->flush();
        
        // Redireccionar a la ruta donde estan todas las notificaciones restantes
        return $this->redirectToRoute("unseen_notifications");
    }
    
    /**
     * @Route("/check-all", name="check_all_notifications")
     */
    public function checkAllNotifications() {
        
        // Llamar a la funcion del repositorio de notificaciones que actualizara todas las notificaciones a vistas / leidas por el usuario
        $this->getDoctrine()->getRepository(Notification::class)->markAllAsReadByUser($this->getUser());
        
        // Actualizar los cambios con el entityManager
        $this->getDoctrine()->getManager()->flush();
        
        return $this->redirectToRoute("unseen_notifications"); 
    }

}
