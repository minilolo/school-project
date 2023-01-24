<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>.
 **/

namespace App\Controller\Student;

use App\Constant\MessageConstant;
use App\Constant\RoleConstant;
use App\Controller\AbstractBaseController;
use App\Entity\ClassRoom;
use App\Entity\Ecolage;
use App\Entity\Student;
use App\Form\EcolageType;
use App\Form\StudentType;
use App\Helper\HistoryHelper;
use App\Manager\SekolikoEntityManager;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Payment;
use App\Repository\PaymentRepository;

/**
 * Class StudentController.
 *
 * @Route("/{_locale}/admin/student")
 */
class StudentController extends AbstractBaseController
{
    /**
     * @var PaymentRepository
     */
    private $repositoryPayer;

    /**
     * @var StudentRepository
     */
    private $repository;

    /**
     * @var HistoryHelper
     */
    private $historyHelper;

    /**
     * StudentController constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param SekolikoEntityManager        $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param StudentRepository            $repository
     * @param HistoryHelper                $historyHelper
     */
    public function __construct(EntityManagerInterface $manager, SekolikoEntityManager $entityManager, UserPasswordEncoderInterface $passwordEncoder, StudentRepository $repository, HistoryHelper $historyHelper)
    {
        parent::__construct($manager, $entityManager, $passwordEncoder);
        $this->repository = $repository;
        $this->historyHelper = $historyHelper;
    }

    /**
     * @Route("/list/{id}",name="student_list",methods={"GET","POST"})
     *
     * @param ClassRoom $class
     *
     * @return Response
     */
    public function list(ClassRoom $class)
    {
        
        
        $studentList = $this->repository->findByClassSchoolYearField($this->getUser(), $class);
        
        
        
        
        
        
        
        return $this->render(
            'admin/content/student/_student_list.html.twig',
            [
                'students' => $studentList,
                'classe' => $class,
                
                
            ]
        );
    }

    /**
     * @Route("/ecolageStudent/{classe}",name="student_ecolage",methods={"GET","POST"})
     *
     * @param ClassRoom $class
     *
     * @return Response
     */
    public function StudentEcolage(ClassRoom $classe, PaymentRepository $repositoryPayer) 
    {
        $studentList = $this->repository->findByClassSchoolYearField($this->getUser(), $classe);
        $date = date('y-m-d');
        $datemonth = date('y-m');
        $datemonth = $datemonth . "-01";
        $payment = $repositoryPayer->findByMonth($date, $datemonth);
        
        if ((sizeof($payment)) >= 1){
            for ($i = 0; $i <= (sizeof($payment) - 1); $i++) {
                $studentPayer = $this->repository->findByEcolage($this->getUser(), $classe, $payment[$i]);
                $StudentFarany = array_push($studentPayer);
            }
        }
        
        
        
        return $this->render(
            'admin/content/student/_student_ecolage.html.twig',
            [
                
                'students2' => $studentPayer,
                'classe' => $classe,
                
                
            ]
        );
    }

    /**
     * @Route("/manage/{classe}/{id?}",name="student_manage",methods={"GET","POST"})
     *
     * @param Request      $request
     * @param ClassRoom    $classe
     * @param Student|null $student
     *
     * @return Response
     */
    public function manage(Request $request, ClassRoom $classe, Student $student = null)
    {
        $student = $student ?? new Student();
        $form = $this->createForm(
            StudentType::class,
            $student,
            [
                'etsName' => $this->getUser()->getEtsName(),
                'classe' => $classe,
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->beforeStudentPersist($classe, $student, $form);
            if ($this->em->save($student, $this->getUser(), $form)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::AJOUT_MESSAGE);

                return $this->redirectToRoute('student_list', ['id' => $classe->getId()]);
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);

            return $this->redirectToRoute(
                'student_manage',
                [
                    'classe' => $classe->getId(),
                    'id' => $student->getId(),
                ]
            );
        }

        return $this->render(
            'admin/content/student/_student_manage.html.twig',
            [
                'form' => $form->createView(),
                'classe' => $classe,
            ]
        );
    }

    /**
     * @param ClassRoom     $classe
     * @param Student       $student
     * @param FormInterface $form
     *
     * @return Student
     */
    public function beforeStudentPersist(ClassRoom $classe, Student $student, FormInterface $form): Student
    {
        $student->getUser()->setPassword(
            $this->passencoder->encodePassword(
                $student->getUser(),
                $form->get('user')->get('password')->getData()
            )
        );
        $student->getUser()->setRoles([RoleConstant::ROLE_SEKOLIKO['Etudiant']]);
        if (!$student->getId()) {
            $this->historyHelper->addHistory(
                'Ajout '.$student->getUser()->getUsername().' dans la classe '.$classe->getName(),
                $student->getUser()
            );
        }

        return $student;
    }

    /**
     * @Route("/details/{id}",name="etudiant_details")
     *
     * @param Student $student
     *
     * @return Response
     */
    public function details(Student $student): Response
    {
        return $this->render('admin/content/student/_details.html.twig', ['student' => $student]);
    }

    /**
     * @Route("/remove/{id}",name="student_remove")
     *
     * @param Student $student
     *
     * @return RedirectResponse
     */
    public function remove(Student $student)
    {
        $classe = $student->getClasse()->getId();
        if ($this->em->remove($student)) {
            $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUPPRESSION_MESSAGE);

            return $this->redirectToRoute('student_list', ['id' => $classe]);
        }
        $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);

        return $this->redirectToRoute('student_list', ['id' => $classe]);
    }

    /**
     * @Route("/ecolage/{id}", name="paiement_ecolage", methods={"POST","GET"})
     *
     * @param Request $request
     * @param Student $student
     *
     * @return Response
     */
    public function paidEcolage(Request $request, Student $student)
    {
        $ecolage = new Ecolage();
        $form = $this->createForm(EcolageType::class, $ecolage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ecolage->setIsPaid(true);
            $ecolage->setPrice($request->get('montant'));
            $student->addEcolage($ecolage);

            $this->manager->flush();

            return $this->redirectToRoute('etudiant_details', ['id' => $student->getId()]);
        }

        return $this->render(
            'admin/content/student/_ecolage_paiement.html.twig',
            ['form' => $form->createView(), 'student' => $student]
        );
    }


}
