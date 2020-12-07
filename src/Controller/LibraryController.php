<?php

namespace App\Controller;

use App\Entity\Libro;
use App\Repository\LibroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController  {

    // private $logger;

    // public function __construct(LoggerInterface $logger){

    //     $this -> logger = $logger;
    // }

    /**
     * @Route("/libros/list",name="libros_list")
     * 
     */

    public function list(Request $request, LibroRepository $repository){

        $libros = $repository -> findAll();

        $array = [];
        $response = new JsonResponse();

        foreach ($libros as $libro){

           $array[] = [
                      'id' => $libro -> getId(),
                      'title' => $libro -> getTitle(),
                      'image' => $libro -> getImage()
            ]; 


        }

        $response->setData([                            
            'success' => true,
            'data' => $array
        ]);
     
        
        
        return $response;


    }

    /**
     * @Route("/libro/crear",name="crear_libro")
     * 
     */

    public function crearLibro (Request $request, EntityManagerInterface $em){

           
            $libro = new Libro ();
            $response = new JsonResponse();
            $title = $request -> get('title', null);
            
            if (empty($title)){

                $response->setData([

                            
                    'success' => false,
                    'error' => 'Title cannot be empty',
                    'data' => null
                ]);
    
                return $response;


            }
            $libro -> setTitle($title);
            $em->persist($libro);
            $em->flush();

            
            $response->setData([
                            
                'success' => true,
                'data' => [

                    [   
                        'id' => $libro -> getId(),
                        'title' => $libro -> getTitle()
                    ],
                  
                ]
            ]);

            return $response;     


    }


}