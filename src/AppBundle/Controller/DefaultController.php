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
        $host_name  = "db680319702.db.1and1.com";
        $database   = "db680319702";
        $user_name  = "dbo680319702";
        $password   = "cepsa123";


        $connect = mysqli_connect($host_name, $user_name, $password, $database);

        if(mysqli_connect_errno())
        {
            var_dump('La connexion au serveur MySQL a échoué: '.mysqli_connect_error());
        }
        else
        {
            dump('Connexion au serveur MySQL établie avec succès');
        }
        // replace this example code with whatever you need
        return $this->render('default/myindex.html.twig');
    }
}
