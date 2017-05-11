<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            $clients = $em->getRepository('AppBundle:Client')->findByCode($client->getCode());
            if(!$clients){
                $this->get('session')->getFlashBag()->set('error', 'Le N° de participation anonyme n\'existe pas.');
            }
            else {
                $myClient=$clients[0];
                if($myClient->getStatus('0'))
                {
                    $myClient->setStatus('1');
                    $em->flush();
                    $this->get('session')->set('user', $myClient->getCode());
                    return $this->redirectToRoute('first_question');
                }
            }
        }


        return $this->render('default/myindex.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/1", name="first_question")
     */
    public function firstAction(Request $request)
    {
        if(!$this->get('session')->get('user')){
            $this->get('session')->getFlashBag()->set('error', 'Vous n\'êtes pas connecté.');
            return  $this->redirectToRoute('homepage');
        }
        $client = $this->get('session')->get('user');

        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('AppBundle:Client')->findOneByCode($client);
        $page = $em->getRepository('AppBundle:Page')->findOneById(1);
        $questions = $em->getRepository('AppBundle:Question')->findByPageId(1);
        $choices = explode("||", $page->getChoix());
        dump($questions);

        if(isset($_POST['submit'])){
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
            die;
        }
        /*$form = $this->createFormBuilder($question)
            ->add('code', TextType::class, array('label' => false))
            ->add('save', SubmitType::class, array('label' => 'VALIDER'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $clients = $em->getRepository('AppBundle:Client')->findByCode($client->getCode());
            if(!$clients){
                $this->get('session')->getFlashBag()->set('error', 'Le N° de participation anonyme n\'existe pas.');
            }
            else {
                $myClient=$clients[0];
                $this->get('security.token_storage')->getToken()->setUser($myClient->getCode());
                if($myClient->getStatus('0'))
                {
                    $myClient->setStatus('1');
                    $em->flush();
                }
            }
        }
*/

        return $this->render('default/questions.html.twig', array(
            //'form' => $form->createView(),
            'page' => $page,
            'questions' => $questions,
            'choices' => $choices,
        ));
    }
}
