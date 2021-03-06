<?php

namespace SumoCoders\FrameworkMultiUserBundle\Command;

use SumoCoders\FrameworkMultiUserBundle\Exception\InterfaceNotImplemented;
use SumoCoders\FrameworkMultiUserBundle\User\UserInterface;
use SumoCoders\FrameworkMultiUserBundle\User\PasswordReset as UserPasswordReset;

class RequestPasswordReset
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * PasswordResetRequest constructor.
     *
     * @param UserInterface $user
     *
     * @throws InterfaceNotImplemented
     */
    public function __construct(UserInterface $user)
    {
        if (!$user instanceof UserPasswordReset) {
            throw new InterfaceNotImplemented('The user doesn\'t implement PasswordReset');
        }

        $this->user = $user;
    }

    /**
     * @return UserPasswordReset|UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
