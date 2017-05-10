<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $task = new Task();

        $form = $this->createFormBuilder($task)
            ->add('user', TextType::class, array('class' => 'Essai'))
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        return $this->render('default/myindex.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
