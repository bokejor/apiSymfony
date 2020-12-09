<?php

namespace App\Controller\Api;

use App\Entity\Libro;
use App\Form\Model\LibroDto;
use App\Form\Type\LibroFormType;
use App\Repository\LibroRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function postAction(
        EntityManagerInterface $em, 
        Request $request, 
        FileUploader $fileUploader) 
    {
        $libroDto = new LibroDto ();
        $form = $this->createForm(LibroFormType::class, $libroDto);  
        $form -> handleRequest ($request);

        if (!$form -> isSubmitted()){

            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($form -> isValid ()){
            
            
            $libro = new Libro ();
            $libro -> setTitle ($libroDto -> title);

            if ($libroDto -> base64Image){

                $filename = $fileUploader -> uploadBase64File ($libroDto -> base64Image);
                $libro -> setImage ($filename);
            }

            $em -> persist ($libro);
            $em -> flush ();
            return $libro;
        }

        return $form;
             
        
    }
}