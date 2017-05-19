<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $fin=11;

        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('AppBundle:Client')->findOneByCode($client);
        if($client->getStatus()>=$number && $number!=0){
            if ($client->getStatus()==$fin && $number==$fin) {
                if($client->getTombola()==1){
                    return $this->render('default/end.html.twig', array('tombola'=>false, 'client'=>$client->getToken()));
                }
                else{
                    return $this->render('default/end.html.twig', array('tombola'=>true, 'client'=>$client->getToken()));
                }
            }
            else {
                $page = $em->getRepository('AppBundle:Page')->findOneById($number);
            }
        }
        else {
            return $this->redirectToRoute('question', array('number'=>$client->getStatus()));
        }

        if($number==1){
            $form = $this->createFormBuilder($client)
                //CHOICE IS FOR SELECT
                ->add('sexe', ChoiceType::class, array('choices' => array("Un homme" =>0,"Une femme"=>1),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true,'required' => true,'data' => 'male'))
                ->add('age', ChoiceType::class, array('choices' => array('placeholder' => false, "Moins de 25 ans"=>0, "de 25 à 34 ans"=>1, "de 35 à 44 ans"=>2, "de 45 à 54 ans"=>3, "55 et plus"=>4),'required' => true,'data' => null))
                ->add('profession', ChoiceType::class, array('choices' => array('Choose an option' => 'placeholder', "Apprenti"=>0, "Ouvrier"=>1, "Employé ou technicien"=>2, "cadre"=>3, "Autre"=>4),'required' => true,'data' => 'null'))
                ->add('enfant', ChoiceType::class, array('choices' => array("Oui"=>0, "Non"=>1),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true,'required' => true,'data' => null))
                ->add('enfant_age', ChoiceType::class, array('choices' => array('placeholder' => false, "0-5 ans"=>0, "6-11 ans"=>1, "12-15 ans"=>2, "16-18 ans"=>3, "Plus de 18 ans"=>4),'required' => false,'data' => null))
                ->add('situation', ChoiceType::class, array('choices' => array('placeholder' => false, "Célibataire"=>0, "En couple"=>1, "Marié(e) / Pacsé(e)"=>2, "Séparé/Divorcé"=>3, "Famille recomposée"=>4),'required' => true,'data' => null))
                ->add('save', SubmitType::class, array('label' => 'suivant'))
                ->getForm();



            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $client->setStatus($number+1);
                $em->flush();
                return $this->redirectToRoute('question', array('number'=>$number+1));
            }
        }
        elseif($number==9){

          /*            $query = $em->createQuery(
                'SELECT c
            FROM AppBundle:Reponse c
            WHERE c.questionId >= :minQ AND c.questionId <= :maxQ'
            )->setParameter('minQ', 26)->setParameter('maxQ' ,35);

            $reponses = $query->getResult();*/

            if(isset($_POST['submit2'])){

                if($client->getStatus()<=$number){
                    $client->setStatus($number+1);
                    $em->flush();
                }

                for($i=1;$i<=10;$i++){

                    $qv = $_POST['input_'.$i];

                    $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                        array('questionId' => (46+$i), 'clientId' => $client->getCode())
                    );

                    if($reponse){
                        $reponse->setQuestionId((46+$i));
                        $reponse->setClientId($client->getCode());
                        $reponse->setValue($qv);
                        $reponse->setMore('');
                        $em->persist($reponse);
                        $em->flush();
                    }
                    else {
                        $response = new Reponse();
                        $response->setQuestionId((46+$i));
                        $response->setClientId($client->getCode());
                        $response->setValue($qv);
                        $response->setMore('');
                        $em->persist($response);
                        $em->flush();
                    }
                }

                return $this->redirectToRoute('question', array('number'=>$number+1));
            }

            return $this->render('default/page_9.html.twig', array(
            ));
        }

        elseif($number==10){

            $query = $em->createQuery(
                  'SELECT c
              FROM AppBundle:Reponse c
              WHERE c.questionId >= :minQ AND c.questionId <= :maxQ'
              )->setParameter('minQ', 57)->setParameter('maxQ' ,67);

              $reponses = $query->getResult();

            if(isset($_POST['submit2'])){

                if($client->getStatus()<=$number){
                    $client->setStatus($number+1);
                    $em->flush();
                }
                $k=1;
                for($i=1;$i<=10;$i++){
                    if($i<=5){
                        $qv = $_POST['espace_'.$i];
                    }
                    else {
                        $qv = $_POST['comm_'.$k];
                        $k++;
                    }


                    $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                        array('questionId' => (56+$i), 'clientId' => $client->getCode())
                    );

                    if($reponse){
                        $reponse->setQuestionId((56+$i));
                        $reponse->setClientId($client->getCode());
                        $reponse->setValue($qv);
                        $reponse->setMore('');
                        $em->persist($reponse);
                        $em->flush();
                    }
                    else {
                        $response = new Reponse();
                        $response->setQuestionId((56+$i));
                        $response->setClientId($client->getCode());
                        $response->setValue($qv);
                        $response->setMore('');
                        $em->persist($response);
                        $em->flush();
                    }
                }

                return $this->redirectToRoute('question', array('number'=>$number+1));
            }

            return $this->render('default/page_10.html.twig', array('responses' => $reponses
            ));
        }

        elseif($number==6){

            if(isset($_POST['submit2'])){

                if($client->getStatus()<=$number){
                    $client->setStatus($number+1);
                    $em->flush();
                }
                $k=0;
                for($i=1;$i<=10;$i++){
                    if($i<=5){
                        $qv = $_POST['select_'.$i];
                    }
                    else {
                        $k++;
                        $qv = $_POST['select_'.$k.'_n'];
                    }

                    $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                        array('questionId' => (25+$i), 'clientId' => $client->getCode())
                    );

                    if($reponse){
                        $reponse->setQuestionId((25+$i));
                        $reponse->setClientId($client->getCode());
                        $reponse->setValue($qv);
                        $reponse->setMore('');
                        $em->persist($reponse);
                        $em->flush();
                    }
                    else {
                        $response = new Reponse();
                        $response->setQuestionId((25+$i));
                        $response->setClientId($client->getCode());
                        $response->setValue($qv);
                        $response->setMore('');
                        $em->persist($response);
                        $em->flush();
                    }
                }

                return $this->redirectToRoute('question', array('number'=>$number+1));
            }

            $query = $em->createQuery(
                'SELECT c
            FROM AppBundle:Reponse c
            WHERE c.questionId >= :minQ AND c.questionId <= :maxQ'
            )->setParameter('minQ', 26)->setParameter('maxQ' ,35);

            $reponses = $query->getResult();

            return $this->render('default/page_5.html.twig', array('responses' => $reponses
            ));
        }

        else{
            $questions = $em->getRepository('AppBundle:Question')->findByPageId($number);
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                        'SELECT c
            FROM AppBundle:Reponse c
            WHERE c.questionId >= :minQ AND c.questionId <= :maxQ'
            )->setParameter('minQ', $questions[0])->setParameter('maxQ' ,end($questions));

            $reponses = $query->getResult();
            //$reponses = $em->getRepository('AppBundle:Reponse')->findBy(array('client'=>$client->getCode(), 'questionId'=>5));
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
        }

        if($number==1){
            return $this->render('default/personnalisation.html.twig', array(
                'form' => $form->createView(),
                'client'=> $client,
            ));
        }
        else {
            return $this->render('default/questions.html.twig', array(
                //'form' => $form->createView(),
                'page' => $page,
                'questions' => $questions,
                'choices' => $choices,
                'reponses' => $reponses,
            ));
        }


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
