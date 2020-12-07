<?php

namespace App\Controller\Api;

use App\Entity\Libro;
use App\Repository\LibroRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;


class LibroController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/libros")
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(LibroRepository $repository) {
        return $repository -> findAll();
    }

    /**
     * @Rest\Post(path="/libros")
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(EntityManagerInterface $em) {
        $libro = new Libro();
        $libro -> setTitle ("El hobbit");
        // $form = $this->createForm(LibroFormType::class, $libro);  
        // $form -> handleRequest ($request);

        // if ($form -> isSubmitted () && $form -> isValid ()){

            $em -> persist ($libro);
            $em -> flush ();
            return $libro;
        // }

        // return $form;
             
        
    }
}