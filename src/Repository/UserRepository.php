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

namespace App\Repository;

use App\Entity\User;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User repository.
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * User repository constructor.
     *
     * @param RegistryInterface $registry parameter injected
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find one user by his mail.
     *
     * @param string $mail mail to search in repository
     *
     * @return User|null
     */
    public function findOneByMail(string $mail): ?User
    {
        return $this->findOneBy(['mail' => $mail]);
    }

    /**
     * Find a user by token.
     *
     * @param string|null $token token in predicate
     *
     * @return User|null
     */
    public function findOneByToken(?string $token): ?User
    {
        try {
            $today = new DateTimeImmutable();
            $yesterday = $today->sub(new DateInterval('P1D'));

            $queryBuilder = $this->createQueryBuilder('u');

            return $queryBuilder->where('u.resettingToken = :token')
                ->andWhere('u.resettingAt > :yesterday')
                ->setParameter('token', $token)
                ->setParameter('yesterday', $yesterday)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Load user by username.
     *
     * @param string $username Username to find
     *
     * @throws UsernameNotFoundException when no user has this username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->findOneByMail($username);
        if (!$user) {
            throw new UsernameNotFoundException('No user found for username '.$username);
        }

        return $user;
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user user to refresh
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        /** @var UserInterface $refreshedUser */
        /** @var User $user */
        if (!$refreshedUser = $this->find($user->getId())) {
            throw new UsernameNotFoundException(sprintf('User with id %s not found', json_encode($user->getId())));
        }

        return $refreshedUser;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class class to test support
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
}
