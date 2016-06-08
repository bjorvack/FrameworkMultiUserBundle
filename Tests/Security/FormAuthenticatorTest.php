<?php

namespace SumoCoders\FrameworkMultiUserBundle\Tests\Security;

use PHPUnit_Framework_TestCase;
use SumoCoders\FrameworkMultiUserBundle\Security\FormAuthenticator;
use SumoCoders\FrameworkMultiUserBundle\Security\FormCredentials;
use SumoCoders\FrameworkMultiUserBundle\Security\ObjectUserProvider;
use SumoCoders\FrameworkMultiUserBundle\User\InMemoryUserRepository;
use SumoCoders\FrameworkMultiUserBundle\User\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class FormAuthenticatorTest extends PHPUnit_Framework_TestCase
{
    private $formAuthenticator;
    private $router;

    public function setUp()
    {
        $this->router = new Router(new Container(), '');
        $encoders['SumoCoders\FrameworkMultiUserBundle\User\User'] = array (
            'class' => 'Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder',
            'arguments' => array(12),
        );
        $encoder = new UserPasswordEncoder(new EncoderFactory($encoders));
        $redirectRoutes = [];
        $this->formAuthenticator = new FormAuthenticator($encoder, $this->router, $redirectRoutes);
    }

    public function testFormAuthenticatorGetUser()
    {
        $provider = new ObjectUserProvider(new InMemoryUserRepository());
        $user = $this->formAuthenticator->getUser($this->getCredentials(), $provider);

        $this->assertEquals($this->getUser(), $user);
    }

    public function testCheckCredentials()
    {
        $this->assertTrue(
            $this->formAuthenticator->checkCredentials($this->getCredentials(), $this->getUser())
        );
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testBadCredentialsException()
    {
        $this->expectException(
            'Symfony\Component\Security\Core\Exception\BadCredentialsException',
            $this->formAuthenticator->checkCredentials($this->getCredentials('wouter', 'wrongPassword'), $this->getUser())
        );
    }

    public function testOnAuthenticationSuccess()
    {
        $request = new Request();
        $providerKey = 'main';
        $token = new UsernamePasswordToken('wouter', null, $providerKey, ['ROLE_USER']);

        $session = new Session(new MockArraySessionStorage());
        $session->set('_security_'.$providerKey, serialize($token));
        $session->set('_security.'.$providerKey.'.target_path', '/randomURL');
        $session->save();
        $request->setSession($session);

        $this->formAuthenticator->onAuthenticationSuccess($request, $token, $providerKey);
    }

    private function getCredentials($username = 'wouter', $password = 'test')
    {
        return new FormCredentials($username, $password);
    }

    private function getUser($username = 'wouter', $password = 'test', $displayName = 'Wouter Sioen'){
        return new User($username, $password, $displayName);
    }
}