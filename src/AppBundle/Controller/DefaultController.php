<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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

            $myClient = $this->getDoctrine()->getRepository('AppBundle:Client')->findByCode($client->getCode());
            if(!$myClient){
                $this->get('session')->getFlashBag()->set('error', 'Le N° de participation anonyme n\'existe pas.');
            }
            else {
                $myClient->setStatus('1');
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('task_success');
            }
        }


        return $this->render('default/myindex.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
