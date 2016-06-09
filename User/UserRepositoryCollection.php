<?php

namespace SumoCoders\FrameworkMultiUserBundle\User;

use Doctrine\Common\Collections\ArrayCollection;
use SumoCoders\FrameworkMultiUserBundle\Exception\RepositoryNotRegisteredException;

class UserRepositoryCollection
{
    /**
     * @var array
     */
    private $userRepositories;

    /**
     * UserRepositoryCollection constructor.
     *
     * @param array $userRepositories
     */
    public function __construct(array $userRepositories)
    {
        $this->userRepositories = new ArrayCollection();
        foreach ($userRepositories as $repository) {
            $this->addUserRepository($repository);
        }
    }

    /**
     * Registers the UserRepository to the UserRepositoryCollection.
     *
     * @param UserRepository $userRepository
     */
    public function addUserRepository(UserRepository $userRepository)
    {
        $this->userRepositories[] = $userRepository;
    }

    /**
     * Get the userRepositories.
     *
     * @return array
     */
    public function all()
    {
        return $this->userRepositories;
    }

    /**
     * Find the UserRepository for a given User Class.
     *
     * @param $className
     *
     * @throws RepositoryNotRegisteredException
     *
     * @return UserRepository
     */
    public function findRepositoryByClassName($className)
    {
        foreach ($this->userRepositories as $repository) {
            if ($repository->supportsClass($className)) {
                return $repository;
            }
        }

        throw RepositoryNotRegisteredException::withClassName($className);
    }
}
