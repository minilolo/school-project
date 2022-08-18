<?php

namespace App\Controller\Assiduite;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AssiduiteType;
use App\Entity\Assiduite;
use App\Entity\User;
use App\Repository\UserRepository;

class AssiduiteController extends AbstractController
{
    /**
     * @Route("/assiduite", name="assiduite")
     */
    public function index(): Response
    {

        $assiduite = new Assiduite();

        $form = $this->createForm(AssiduiteType::class, $assiduite);
        return $this->render('assiduite/index.html.twig', [
            'controller_name' => 'AssiduiteController',
            'form' => $form->createView(),
        ]);
    }


}
