<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $client = new Client();



        $userProvider = new InMemoryUserProvider(
            array(
                'admin' => array(
                    // password is "foo"
                    'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',
                    'roles'    => array('ROLE_ADMIN'),
                ),
            )
        );


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
                $myClient->setStatus('1');
                $em->flush();
            }
        }


        return $this->render('default/myindex.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
