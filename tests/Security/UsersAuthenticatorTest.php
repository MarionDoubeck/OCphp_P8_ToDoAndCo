<?php
use App\Security\UsersAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UsersAuthenticatorTest extends TestCase
{
    public function testAuthenticate()
    {
        // Setup
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $session = $this->createMock(SessionInterface::class);
        $authenticator = new UsersAuthenticator($urlGenerator);
        $request = new Request([], ['username' => 'testuser', 'password' => 'testpassword', '_csrf_token' => 'valid_csrf_token']);
        $request->setSession($session);

        // Execution
        $passport = $authenticator->authenticate($request);

        // Assertion
        $this->assertInstanceOf(\Symfony\Component\Security\Http\Authenticator\Passport\Passport::class, $passport);
        // Add more assertions as needed
    }

    public function testOnAuthenticationSuccess()
    {
        // Setup
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('/main');
        $authenticator = new UsersAuthenticator($urlGenerator);
        $request = new Request();
        $session = $this->createMock(SessionInterface::class);
        $request->setSession($session);
        $token = $this->createMock(TokenInterface::class);

        // Execution
        $response = $this->invokeMethod($authenticator, 'onAuthenticationSuccess', [$request, $token, 'main_firewall']);

        // Assertion
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\RedirectResponse::class, $response);
        $this->assertEquals('/main', $response->getTargetUrl());
    }

    public function testGetLoginUrl()
    {
        // Setup
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('/login');
        $authenticator = new UsersAuthenticator($urlGenerator);
        $request = new Request();

        // Execution
        $loginUrl = $this->invokeMethod($authenticator, 'getLoginUrl', [$request]);

        // Assertion
        $this->assertEquals('/login', $loginUrl);
    }

    // Helper method to invoke protected/private methods
    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
