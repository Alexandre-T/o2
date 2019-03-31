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

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface Entity manager
     */
    private $entityManager;

    /**
     * UserProvider constructor.
     *
     * @param EntityManagerInterface $entityManager entity manager provided by dependencies injection
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @param string $username mail of user
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        // Load a User object from your data source or throw UsernameNotFoundException.
        // The $username argument may not actually be a username:
        // it is whatever value is being returned by the getUsername()
        // method in your User class.
        $user = $this->entityManager
            ->getRepository('App:User')
            ->findOneBy(['mail' => $username])
        ;

        if (!$user instanceof UserInterface) {
            throw new UsernameNotFoundException('TODO: fill in loadUserByUsername() inside '.__FILE__);
        }

        return $user;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: false" (for a pure API), this
     * method is not called.
     *
     * @param UserInterface $user user to refresh
     *
     * @throws UnsupportedUserException  when $user is not a User entity
     * @throws UsernameNotFoundException when $user is anymore stored
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        // Return a User object after making sure its data is "fresh".
        // Or throw a UsernameNotFoundException if the user no longer exists.
        $user = $this->entityManager
            ->getRepository('App:User')
            ->findOneBy(['mail' => $user->getUsername()])
        ;

        if (!$user instanceof UserInterface) {
            throw new UsernameNotFoundException('TODO: fill in loadUserByUsername() inside '.__FILE__);
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     *
     * @param string $class class to test
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
