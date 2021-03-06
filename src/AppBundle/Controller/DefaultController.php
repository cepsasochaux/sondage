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
            $code = $client->getCode();

            $code  = array_map('intval', str_split($code));
            if($code[2]==0){
                if($code[1]==0){
                    if($code[0]==0){
                        unset($code[2]);
                        unset($code[1]);
                        unset($code[0]);
                    }
                }
                else {
                    if($code[0]==0){
                        unset($code[0]);
                    }
                }
            }
            else {
                if($code[1]==0){
                    if($code[0]==0){
                        unset($code[1]);
                        unset($code[0]);
                    }
                }
                else {
                    if($code[0]==0){
                        unset($code[0]);
                    }
                }
            }
            $code = implode("",$code);

            $clients = $em->getRepository('AppBundle:Client')->findOneByCode($code);
                if(!$clients){
                $this->get('session')->getFlashBag()->set('error', 'Le N° de participation anonyme n\'existe pas.');
                return  $this->redirectToRoute('homepage');
            }
            else{
                if($clients->getStatus()==0)
                {
                    $token = "AeOI".random_int(0,1000)."ZD".random_int(0,1000)."e".random_int(0,1000)."Mp";
                    $testToken = $em->getRepository('AppBundle:Client')->findOneByToken($token);
                    while ($testToken) {
                        $token = "AeOI".random_int(0,1000)."ZD".random_int(0,1000)."e".random_int(0,1000)."Mp";
                        $testToken = $em->getRepository('AppBundle:Client')->findOneByToken($token);
                    }
                    $clients->setStatus(1);
                    $clients->setToken($token);
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
     * @Route("/admin/cepsa/new", name="admin")
     */
    public function adminAction(Request $request)
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
            $code = $client->getCode();

            $code  = array_map('intval', str_split($code));
            if($code[2]==0){
                if($code[1]==0){
                    if($code[0]==0){
                        unset($code[2]);
                        unset($code[1]);
                        unset($code[0]);
                    }
                }
                else {
                    if($code[0]==0){
                        unset($code[0]);
                    }
                }
            }
            else {
                if($code[1]==0){
                    if($code[0]==0){
                        unset($code[1]);
                        unset($code[0]);
                    }
                }
                else {
                    if($code[0]==0){
                        unset($code[0]);
                    }
                }
            }
            $code = implode("",$code);

            $clients = $em->getRepository('AppBundle:Client')->findOneByCode($code);
            if(!$clients){
                $this->get('session')->getFlashBag()->set('error', 'Le N° de participation anonyme n\'existe pas.');
                return  $this->redirectToRoute('homepage');
            }
            else{
                if($clients->getStatus()==0)
                {
                    $token = "AeOI".random_int(0,1000)."ZD".random_int(0,1000)."e".random_int(0,1000)."Mp";
                    $testToken = $em->getRepository('AppBundle:Client')->findOneByToken($token);
                    while ($testToken) {
                        $token = "AeOI".random_int(0,1000)."ZD".random_int(0,1000)."e".random_int(0,1000)."Mp";
                        $testToken = $em->getRepository('AppBundle:Client')->findOneByToken($token);
                    }
                    $clients->setStatus(1);
                    $clients->setToken($token);
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


        return $this->render('default/myadmin.html.twig', array(
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
        if($number!=0){
            if ( $number==$fin) {
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
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true,'required' => false))
                ->add('age', ChoiceType::class, array('placeholder' => 'Choisissez','required' => false, 'choices' => array("Moins de 25 ans"=>0, "de 25 à 34 ans"=>1, "de 35 à 44 ans"=>2, "de 45 à 54 ans"=>3, "55 et plus"=>4)))
                ->add('profession', ChoiceType::class, array('placeholder' => 'Choisissez','required' => false, 'choices' => array("Apprenti"=>0, "Ouvrier"=>1, "Employé ou Technicien"=>2, "Cadre"=>3, "Autre"=>4)))
                ->add('enfant', ChoiceType::class, array('choices' => array("Oui"=>1, "Non"=>0),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true,'required' => false))
                ->add('situation', ChoiceType::class, array('placeholder' => 'Choisissez','required' => false, 'choices' => array("Célibataire"=>0, "En couple"=>1, "Marié(e) / Pacsé(e)"=>2, "Séparé/Divorcé"=>3, "Famille recomposée"=>4)))
                ->add('save', SubmitType::class, array('label' => 'suivant'))
                ->getForm();



            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $client->setEnfant05($_POST['tranche_1']);
                $client->setEnfant611($_POST['tranche_2']);
                $client->setEnfant1216($_POST['tranche_3']);
                $client->setEnfant1618($_POST['tranche_4']);
                $client->setEnfant18($_POST['tranche_5']);

                $client->setStatus(25);
                $em->flush();
                return $this->redirectToRoute('question', array('number'=>$number+1));
            }
        }
        elseif($number==9){

            $query = $em->createQuery(
                'SELECT c
            FROM AppBundle:Reponse c
            WHERE c.questionId >= :minQ AND c.questionId <= :maxQ AND c.codeClient = :client'
              )->setParameter('minQ', 47)->setParameter('maxQ' ,56)->setParameter('client', $client->getCode());

            $reponses = $query->getResult();

            if(isset($_POST['submit2'])){

                if($client->getStatus()<=$number){
                    $client->setStatus($number+1);
                    $em->flush();
                }

                for($i=1;$i<=10;$i++){

                    $qv='';

                    if(isset($_POST['select_'.$i])){
                        $qv = $_POST['select_'.$i];
                    }

                        $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                            array('questionId' => (46+$i), 'codeClient' => $client->getCode())
                        );

                        if($reponse){
                            $reponse->setQuestionId((46+$i));
                            $reponse->setCodeClient($client->getCode());
                            $reponse->setValue($qv);
                            $reponse->setMore('');
                            $em->persist($reponse);
                            $em->flush();
                        }
                        else {
                            $response = new Reponse();
                            $response->setQuestionId((46+$i));
                            $response->setCodeClient($client->getCode());
                            $response->setValue($qv);
                            $response->setMore('');
                            $em->persist($response);
                            $em->flush();
                        }


                }
                return $this->redirectToRoute('question', array('number'=>$number+1));
            }

            return $this->render('default/page_9.html.twig', array('responses' =>$reponses, 'number' => $number
            ));
        }

        elseif($number==10){

            $query = $em->createQuery(
                  'SELECT c
              FROM AppBundle:Reponse c
              WHERE c.questionId >= :minQ AND c.questionId <= :maxQ AND c.codeClient = :client'
              )->setParameter('minQ', 57)->setParameter('maxQ' ,67)->setParameter('client', $client->getCode());

              $reponses = $query->getResult();

            if(isset($_POST['submit2'])){

                if($client->getStatus()<=$number){
                    $client->setStatus($number+1);
                    $em->flush();
                }
                $k=1;
                for($i=0;$i<=10;$i++){
                    $qv='';
                    if($i<=5){
                        if(isset($_POST['espace_'.$i])){
                            $qv = $_POST['espace_'.$i];
                        }

                    }
                    else {
                        if(isset($_POST['comm_'.$k])){
                            $qv = $_POST['comm_'.$k];
                        }

                        $k++;
                    }

                        $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                            array('questionId' => (57+$i), 'codeClient' => $client->getCode())
                        );

                        if($reponse){
                            $reponse->setQuestionId((57+$i));
                            $reponse->setCodeClient($client->getCode());
                            $reponse->setValue($qv);
                            $reponse->setMore('');
                            $em->persist($reponse);
                            $em->flush();
                        }
                        else {
                            $response = new Reponse();
                            $response->setQuestionId((57+$i));
                            $response->setCodeClient($client->getCode());
                            $response->setValue($qv);
                            $response->setMore('');
                            $em->persist($response);
                            $em->flush();
                        }


                }

                return $this->redirectToRoute('question', array('number'=>$number+1));
            }

            return $this->render('default/page_10.html.twig', array('responses' => $reponses, 'number' => $number
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
                    $qv='';
                    if($i<=5){
                        if(isset($_POST['select_'.$i])){
                            $qv = $_POST['select_'.$i];
                        }

                    }
                    else {
                        $k++;
                        if(isset( $_POST['select_'.$k.'_n'])){
                            $qv = $_POST['select_'.$k.'_n'];
                        }

                    }
                        $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                            array('questionId' => (25+$i), 'codeClient' => $client->getCode())
                        );

                        if($reponse){
                            $reponse->setQuestionId((25+$i));
                            $reponse->setCodeClient($client->getCode());
                            $reponse->setValue($qv);
                            $reponse->setMore('');
                            $em->persist($reponse);
                            $em->flush();
                        }
                        else {
                            $response = new Reponse();
                            $response->setQuestionId((25+$i));
                            $response->setCodeClient($client->getCode());
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
            WHERE c.questionId >= :minQ AND c.questionId <= :maxQ AND c.codeClient = :client'
            )->setParameter('minQ', 26)->setParameter('maxQ' ,35)->setParameter('client', $client->getCode());

            $reponses = $query->getResult();

            return $this->render('default/page_5.html.twig', array('responses' => $reponses, 'number' => $number
            ));
        }

        else{
            $questions = $em->getRepository('AppBundle:Question')->findByPageId($number);
            $em = $this->getDoctrine()->getManager();

            /*$query = $em->createQuery(
                        'SELECT c
            FROM AppBundle:Reponse c
            WHERE c.questionId >= :minQ AND c.questionId <= :maxQ AND c.codeClient = :client'
            )->setParameter('minQ', $questions[0])->setParameter('maxQ' ,end($questions))->setParameter('client', $client->getCode());

            $reponses = $query->getResult();*/
            //$reponses = $em->getRepository('AppBundle:Reponse')->findBy(array('client'=>$client->getCode(), 'questionId'=>5));
            $choices = explode("||", $page->getChoix());

            if(isset($_POST['submit'])){
                if($client->getStatus()<=$number){
                    $client->setStatus($number+1);
                    $em->flush();
                }
                foreach ($questions as $question){
                    if(isset($_POST['question_'.$question->getId()])) {
                        $qv = $_POST['question_'.$question->getId()];
                        $qt = $_POST['question_'.$question->getId().'_text'];
                    }
                    else {
                        $qv = '';
                        $qt = '';
                    }

                        $reponse = $em->getRepository('AppBundle:Reponse')->findOneBy(
                            array('questionId' => $question->getId(), 'codeClient' => $client->getCode())
                        );
                        if($reponse){
                            $reponse->setQuestionId($question->getId());
                            $reponse->setCodeClient($client->getCode());
                            $reponse->setValue($qv);
                            $reponse->setMore($qt);
                            $em->persist($reponse);
                            $em->flush();
                        }
                        else {
                            $response = new Reponse();
                            $response->setQuestionId($question->getId());
                            $response->setCodeClient($client->getCode());
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
                'reponses' => "", 'number' => $number
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
                ->add('email', EmailType::class, array('label' => "Email"))
                ->add('telephone', NumberType::class, array('label' => "Téléphone", 'required'=>false))
                ->add('code_personnel', TextType::class, array('label'=> 'Votre code  personnel'))
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
                $to = 'leo.meyer12@gmail.com, mmontmirail@cepsa-sochaux.com';

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
