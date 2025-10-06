<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/login', name: 'app_login_post', methods: ['POST'])]
    public function loginPost(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepo,
        CsrfTokenManagerInterface $csrfManager
    ): RedirectResponse {
        $email     = $request->request->get('email');
        $password  = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // 1. CSRF
        if (!$csrfManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_login');
        }

        // 2. Utilisateur
        $user = $userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            $this->addFlash('error', 'Identifiants incorrects.');
            return $this->redirectToRoute('app_login');
        }

        // 3. Mot de passe
        if (!$hasher->isPasswordValid($user, $password)) {
            $this->addFlash('error', 'Identifiants incorrects.');
            return $this->redirectToRoute('app_login');
        }

        // 4. Connexion manuelle
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $request->getSession()->set('_security_main', serialize($token));

        // 5. Redirection
        return $this->redirectToRoute('app_home');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(Security $security): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}