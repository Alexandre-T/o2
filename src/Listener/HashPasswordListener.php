<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Listener;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Hash Password Listener.
 */
class HashPasswordListener implements EventSubscriber
{
    /**
     * The password encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * HashPasswordListener constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder provided by Injection dependencies
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * This subscriber will listen prePersist and preUpdate event.
     *
     * @return array of events this subscriber wants to listen to
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    /**
     * This function is called before persist.
     *
     * @param LifecycleEventArgs $args provided by lifecycle
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            //This is not a User, so we quit.
            return;
        }

        $this->encodePassword($entity);
    }

    /**
     * This function is called before update.
     *
     * @param LifecycleEventArgs $args provided by lifecycle
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }

        $this->encodePassword($entity);

        // I understand why we do that: it is necessary to force the update to see the change
        $entityManager = $args->getEntityManager();
        // But I do not understand how we can find this solution to force this update.
        $meta = $entityManager->getClassMetadata(get_class($entity));
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * Encode the plain password and pass it to the user entity.
     *
     * @param User $entity entity to encode
     */
    private function encodePassword(User $entity): void
    {
        // Is the user password modified?
        if (!$entity->getPlainPassword()) {
            //No, so we quit
            return;
        }

        // Password encoding
        $encoded = $this->passwordEncoder->encodePassword(
            $entity,
            $entity->getPlainPassword()
        );

        // We pass the encoded password to the Entity
        $entity->setPassword($encoded);
    }
}
