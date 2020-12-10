<?php

namespace App\Controller\Api;


use App\Service\LibroFormProcessor;
use App\Service\LibroManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LibroController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/libros")
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(
        LibroManager $libroManager
    ) {
        return $libroManager->getRepository()->findAll();
    }


    /**
     * @Rest\Post(path="/libros")
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(
        LibroManager $libroManager,
        LibroFormProcessor $libroFormProcessor,
        Request $request
    ) {
        $libro = $libroManager->create();
        [$libro, $error] = ($libroFormProcessor)($libro, $request);
        $statusCode = $libro ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $libro ?? $error;
        return View::create($data, $statusCode);    
    }

    /**
     * @Rest\Get(path="/libros/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSingleAction(
        int $id,
        LibroManager $libroManager
    ) {
        $libro = $libroManager->find($id);
        if (!$libro) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }
        return $libro;
    }

    /**
     * @Rest\Post(path="/libros/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
        int $id,
        LibroFormProcessor $libroFormProcessor,
        LibroManager $libroManager,        
        Request $request
    ) {
        $libro = $libroManager->find($id);

        if (!$libro) {

            // throw $this->createNotFoundException('No se encuentra el libro');
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }
        [$libro, $error] = ($libroFormProcessor)($libro, $request);
        $statusCode = $libro ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $libro ?? $error;
        return View::create($data, $statusCode);        
    }     
       

    /**
     * @Rest\Delete(path="/libros/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(
        int $id,
        LibroManager $libroManager
    ) {
        $libro = $libroManager->find($id);
        if (!$libro) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }
        $libroManager->delete($libro);
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}

// $libroDto = LibroDto::createFromBook($libro);

//         $originalCategories = new ArrayCollection();
//         foreach ($libro->getCategories() as $category) {
//             $categoryDto = CategoryDto::createFromCategory($category);
//             $libroDto->categories[] = $categoryDto;
//             $originalCategories->add($categoryDto);
//         }

//         $form = $this->createForm(LibroFormType::class, $libroDto);  
//         $form -> handleRequest ($request);

//         if (!$form -> isSubmitted()){

//             return new Response('', Response::HTTP_BAD_REQUEST);
//         }

//         if ($form->isValid()) {

//             // Remove categories
//             foreach ($originalCategories as $originalCategoryDto) {
//                 if (!in_array($originalCategoryDto, $libroDto->categories)) {
//                     $category = $categoryRepository->find($originalCategoryDto->id);
//                     $libro->removeCategory($category);
//                 }
//             }

//             // Add categories
//             foreach ($libroDto->categories as $newCategoryDto) {
//                 if (!$originalCategories->contains($newCategoryDto)) {
//                     $category = $categoryRepository->find($newCategoryDto->id ?? 0);
//                     if (!$category) {
//                         $category = new Category();
//                         $category->setName($newCategoryDto->name);
//                         $em->persist($category);
//                     }
//                     $libro->addCategory($category);
//                 }
//             }
//             $libro->setTitle($libroDto->title);
//             if ($libroDto->base64Image) {
//                 $filename = $fileUploader->uploadBase64File($libroDto->base64Image);
//                 $libro->setImage($filename);
//             }

//             $em->persist($libro);
//             $em->flush();
//             return $libro;
//         }
//         return $form;

