<?php

namespace App\Security;

use App\Entity\Setting\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadUserByUsername($username): User
    {
        $user = $this->findUserBy(['username' => $username]);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('User with "%s" does not exist.', $username));
        }

        return $user;
    }

    private function findUserBy(array $options): ?User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy($options);

        return $user;
    }

    public function refreshUser(UserInterface $user): User
    {
        /** @var User $user */
        if (null === $reloadedUser = $this->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
