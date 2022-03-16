<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface$entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mot-de-pase', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $notification = null;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $old_password = $form->get('old_password')->getData();
            //dd($old_password);
                //$user = $form->getData();
            if ($passwordHasher->isPasswordValid($this->getUser(), $old_password)) {
                $new_pwd = $form->get('new_password')->getData();
                //dd($new_pwd);
                $password = $passwordHasher->hashPassword($this->getUser(), $new_pwd);
                //dd($password);
                $this->getUser()->setPassword($password);

                //$this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = 'Votre mot de passe a bien été mis à jour';

            } else {
                $notification = "Votre mot de passe actuel n'est pas le bon";

            }
        }

            return $this->render('account/password.html.twig', [
                'form' => $form->createView(),
                'notification' => $notification
            ]);
        }

}

