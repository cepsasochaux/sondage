<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;
use AppBundle\Entity\Page;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $client = new Client();

        $form = $this->createFormBuilder($client)
            ->add('code', TextType::class, array('label' => false))
            ->add('save', SubmitType::class, array('label' => 'VALIDER'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $clients = $em->getRepository('AppBundle:Client')->findOneByCode($client->getCode());
            if(!$clients){
                $this->get('session')->getFlashBag()->set('error', 'Le N° de participation anonyme n\'existe pas.');
                return  $this->redirectToRoute('homepage');
            }
            else{
                if($clients->getStatus()==0)
                {
                    $clients->setStatus(1);
                    $clients->setToken("AeOI".random_int(0,1000)."ZD".random_int(0,1000)."e".random_int(0,1000)."Mp");
                    $em->flush();
                    $this->get('session')->set('user', $clients->getCode());
                    return $this->redirectToRoute('question', array('number' => 1));
                }
                else {
                    $this->get('session')->set('user', $clients->getCode());
                    return $this->redirectToRoute('question', array('number' => $clients->getStatus()));
                }
            }
        }


        return $this->render('default/myindex.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{number}", name="question")
     */
    public function firstAction(Request $request, $number)
    {
        if(!$this->get('session')->get('user')){
            $this->get('session')->getFlashBag()->set('error', 'Vous n\'êtes pas connecté.');
            return  $this->redirectToRoute('homepage');
        }
        $client = $this->get('session')->get('user');
        $fin=2;

        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('AppBundle:Client')->findOneByCode($client);
        if($client->getStatus()>=$number && $number!=0){
            if ($client->getStatus()==$fin && $number==$fin) {
                $client->setStatus(3);
                $em->flush();
                return $this->render('default/end.html.twig', array('tombola'=>true, 'client'=>$client->getToken()));
            }
            elseif ($client->getStatus()==$fin+1 && $number>=$fin) {
                return $this->render('default/end.html.twig', array('tombola'=>false, 'client'=>$client->getToken()));
            }
            else {
                $page = $em->getRepository('AppBundle:Page')->findOneById($number);
            }
        }
        else {
            return $this->redirectToRoute('question', array('number'=>$client->getStatus()));
        }


        $questions = $em->getRepository('AppBundle:Question')->findByPageId($number);
        $reponses = $em->getRepository('AppBundle:Reponse')->findByClientId($client->getCode());
        $choices = explode("||", $page->getChoix());


        if(isset($_POST['submit'])){
            if($client->getStatus()<=$number){
                $client->setStatus($number+1);
                $em->flush();
            }
            foreach ($questions as $question){
                $qv = $_POST['question_'.$question->getId()];
                $qt = $_POST['question_'.$question->getId().'_text'];
                $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                    array('questionId' => $question->getId(), 'clientId' => $client->getCode())
                );
                if($reponse){
                    $reponse->setQuestionId($question->getId());
                    $reponse->setClientId($client->getCode());
                    $reponse->setValue($qv);
                    $reponse->setMore($qt);
                    $em->persist($reponse);
                    $em->flush();
                }
                else {
                    $response = new Reponse();
                    $response->setQuestionId($question->getId());
                    $response->setClientId($client->getCode());
                    $response->setValue($qv);
                    $response->setMore($qt);
                    $em->persist($response);
                    $em->flush();
                }
            }

            return $this->redirectToRoute('question', array('number'=>$number+1));
        }
        return $this->render('default/questions.html.twig', array(
            //'form' => $form->createView(),
            'page' => $page,
            'questions' => $questions,
            'choices' => $choices,
            'reponses' => $reponses,
        ));

    }

    /**
     * @Route("/sondage/{token}", name="sondage")
     */
    public function sendmail(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $tombola = $em->getRepository('AppBundle:Client')->findOneByToken($token);

        if($tombola->getTombola()==1 || !$tombola){
            return $this->render('default/thx.html.twig', array());
        }
        else{
            $form = $this->createFormBuilder(null)
                ->add('lastname', TextType::class, array('label' => "Nom"))
                ->add('firstname', TextType::class, array('label' => "Prénom"))
                ->add('email', EmailType::class, array('label' => "email"))
                ->add('telephone', NumberType::class, array('label' => "téléphone"))
                ->add('code_personnel', NumberType::class, array('label'=> 'Votre code  personnel'))
                ->add('save', SubmitType::class, array('label' => 'VALIDER'))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tombola->setTombola(1);
                $em->flush();
                $client = $form->getData();
                $message = '<html><body>';
                $message .= '<h1>Ticket pour tombola<h1/>';
                $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
                $message .= "<tr style='background: #eee;'><td><strong>Nom:</strong> </td><td>" . $client['lastname'] . "</td></tr>";
                $message .= "<tr><td><strong>Prénom:</strong> </td><td>" . $client['firstname'] . "</td></tr>";
                $message .= "<tr><td><strong>Email:</strong> </td><td>" . $client['email'] . "</td></tr>";
                $message .= "<tr><td><strong>Code personnel:</strong> </td><td>" . $client['code_personnel'] . "</td></tr>";
                $message .= "<tr><td><strong>Téléphone:</strong> </td><td>" . $client['telephone'] . "</td></tr>";
                $message .= "</table>";
                $message .= "</body></html>";
                $to = 'leo.meyer12@gmail.com';

                $subject = 'Tombola ticket virtuel';

                $headers = "From: contact@cepsa-sochaux-sondage.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($to, $subject, $message, $headers);
                return $this->render('default/thx.html.twig', array());

            }

            return $this->render('default/formulaire.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }
}
