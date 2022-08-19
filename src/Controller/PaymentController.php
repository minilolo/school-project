<?php

namespace App\Controller;

use Doctrine\Persistence\ObjectManager;
use App\Constant\MessageConstant;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Manager\SekolikoEntityManager;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PaymentController extends AbstractController
{

    /**
     * @var PaymentRepository
     */
    private $repository;



    
    #[Route('/payment', name: 'app_payment')]
    public function index(PaymentRepository $repository): Response
    {
        $manisa = 0;
        $sortie = 0;
        $benefice = 0;
        $PaymentList = $repository->findAll();
        $entrer = $repository->findBy(
            ['Type' => 'Entrer']
        );
        $sortieArray = $repository->findBy(
            ['Type' => 'Sortie']
        );
       
        
        for ($i = 0; $i <= (sizeof($entrer) - 1); $i++){
                $manisa = $manisa + intval($entrer[$i]->GetMontant());
        }
        for ($i = 0; $i <= (sizeof($sortieArray) - 1); $i++){
            $sortie = $sortie + intval($sortieArray[$i]->GetMontant());
        }
        $benefice = $manisa - $sortie;
        
        return $this->render(
            'payment/index.html.twig',
            [
                'payments' => $PaymentList, 
                'total'  =>   array($manisa),
                'sortie' => array($sortie),
                'benefices' => array($benefice)
            ]
        );
    }

    

    #[Route('/paymentAdd', name: 'payment_create', methods: ["GET", "POST"])]
    public function create(Request $request, EntityManagerInterface $em, Payment $Payment = null)
    {
        

        $Payment = $Payment ?? new Payment;
        $form = $this->createForm(
            PaymentType::class,
            $Payment
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $data = $request->request->all();
            $type = $form->getData();
            
            
            $montantt = 10000;
            $date = new DateTime();
            $Payment->setType($type->getType());
            $Payment->setMotif($type->getMotif());
            $Payment->setMontant(intval($data["payment"]['montant']));
            
            $Payment->setDateEnregistrement($date);
            $Payment->setDatePayment($type->getDatePayment());
            $Payment->setUser($type->getUser());
            $em->persist($Payment);
            $em->flush();
            
            $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::AJOUT_MESSAGE);
            return $this->redirectToRoute('app_payment');
        }
        
        return $this->renderForm('payment/payment_mange.html.twig', [
            'form' => $form,
        ]);


        return $this->render('payment/payment_mange.html.twig');
    }


    
   
}
