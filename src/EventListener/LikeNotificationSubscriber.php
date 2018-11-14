<?php

namespace App\EventListener;

use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Class encargada de los eventos de Like
 */
class LikeNotificationSubscriber implements EventSubscriber {

    /**
     * 
     * @return type
     */
    public function getSubscribedEvents() {

        // Devolver el evento el cual nos queremos subscribir, puede ser onFlush, onUpdate, onDelete, etc.
        return [
            Events::onFlush
        ];
    }

    /**
     * El nombre de la funcion tiene que tener el mismo nombre que el evento definido en la funcion getSubscribedEvents()
     */
    public function onFlush(OnFlushEventArgs $args) {

        // Instancia del entityManager
        $em = $args->getEntityManager();

        // Instancia del unitOfword, class que se encarga de gestionar todos los cambios que se han realizado en las entidades
        $uow = $em->getUnitOfWork();

        // Iterar sobre el listado de colleciones que implementan doctrine
        // De esa lista podemos comprobar si hay algun nuevo elemento, cambio, etc
        foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate) {

            // Comprobar que la collection getOwner sea del tipo MicroPost
            if (!$collectionUpdate->getOwner() instanceof MicroPost) {

                // Si no lo es saltamos a la siguiente iteracion
                continue;
            }

            // Comprobar que el campo modificado de la collection sea el que estamos buscando
            if ("likedBy" != $collectionUpdate->getMapping()["fieldName"]) {

                // Pasamos a la siguiente iteracion, no es el campo que buscamos
                continue;
            }

            // Array de elementos que fueron añadidos a la collection
            $insertDiff = $collectionUpdate->getInsertDiff();

            if (!count($insertDiff)) {

                return;
            }

            // Si llegamos aqui estamo seguros que la class es MicroPost y el campo cambiado es el que buscamos
            // Coger la instancia del MicroPost
            $microPost = $collectionUpdate->getOwner();
            
            // Crear nueva instancia de la entidad LikeNotification
            $notification = new LikeNotification();
            
            // hacer set del user que contiene el post
            $notification->setUser($microPost->getUser());
            
            // hacer set de la instancia del post
            $notification->setMicroPost($microPost);
            
            // la function reset de php nos da el primer elemento de un array, que en este caso sera una instancia de la entidad User
            $notification->setLikedBy(reset($insertDiff));
            
            // Hacer el insert de la notificacion en la BBDD
            $em->persist($notification);
            
            $uow->computeChangeSet($em->getClassMetadata(LikeNotification::class), $notification);
        }
    }

}
