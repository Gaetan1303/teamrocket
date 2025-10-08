<?php
// src/Controller/SbireController.php
namespace App\Controller;

use App\Entity\Sbire;
use App\Form\SbireType;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SbireController extends AbstractController
{
    #[Route('/sbire/creation', name: 'app_sbire_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em, ImageUploader $uploader): Response
    {
        $user = $this->getUser();
        if ($user->getSbire()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $sbire = new Sbire();
        $form = $this->createForm(SbireType::class, $sbire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarType = $form->get('avatarType')->getData();

            switch ($avatarType) {
                case 'default':
                    $sbire->setPicture($form->get('defaultAvatar')->getData());
                    break;
                case 'team':
                    $sbire->setPicture($form->get('teamAvatar')->getData());
                    break;
                case 'custom':
                    $file = $form->get('customAvatar')->getData();
                    if ($file) {
                        $filename = $uploader->uploadAndResize($file, 'characters', 200, 200);
                        $sbire->setPicture('assets/images/characters/' . $filename);
                    }
                    break;
            }

            $sbire->setUser($user);
            $em->persist($sbire);
            $em->flush();

            $this->addFlash('success', 'Avatar sbire créé ! Bienvenue dans la team.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('sbire/create.html.twig', [
            'form' => $form,
            'team' => $user->getTeamVilain(),
        ]);
    }
}