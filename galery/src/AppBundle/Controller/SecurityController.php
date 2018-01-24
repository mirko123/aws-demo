<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error
        ));
    }


    /**
     * @Route("/register", name="security_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $usernameExist = $userRole = $this->getDoctrine()
                ->getRepository(User::class)->findOneBy(["username"=>$user->getUsername()]);
            if($usernameExist) {
                $this->addFlash("error", "User exist");
                return $this->render(':security:register.html.twig', [
//                    'error' => "User exist"
                    'error' => ""
                ]);
            }
            else {
                $password = $this->get("security.password_encoder")->encodePassword($user, $user->getPassword());

                $userRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy(["name"=>"ROLE_USER"]);
                $user->addRole($userRole);
                $user->setPassword($password);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();


            }

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            return $this->redirectToRoute('homepage');
        }

        return $this->render(':security:register.html.twig', ['error' => ""]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute("security_login");
    }


    /**
     * @Route("/profile", name="security_profile")
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction()
    {
        return $this->render(":security:profile.html.twig", ["user" => $this->getUser()]);
    }
}













