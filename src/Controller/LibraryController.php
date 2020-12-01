<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController  {

    public function __construct(){

        
    }

    /**
     * @Route("/library/list",name="library_list")
     * 
     */

    public function list(Request $request){

        $title = $request -> get('title', 'Un cipote con orejas');

        $response = new JsonResponse();
        $response->setData([

                          
            'success' => true,
            'data' => [

                [   
                    'id' => 1,
                    'title' => "El señor de los anillos"
                ],
                [
                    'id' => 2,
                    'title' => "El hobbit"
                ],
                [
                    'id' => 3,
                    'title' => $title
                ]
            ]
        ]);

        return $response;


    }


}