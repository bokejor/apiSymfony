<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\Libro;
use App\Form\Model\CategoryDto;
use App\Form\Model\LibroDto;
use App\Form\Type\LibroFormType;
use App\Repository\CategoryRepository;
use App\Repository\LibroRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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

    /**
     * @Rest\Post(path="/libros/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"libro"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
        int $id,
        EntityManagerInterface $em,
        LibroRepository $bookRepository,
        CategoryRepository $categoryRepository,
        FileUploader $fileUploader,
        Request $request
    ) {
        $book = $bookRepository->find($id);

        if (!$book) {

            throw $this->createNotFoundException('No se encuentra el libro');
            // return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        $libroDto = LibroDto::createFromBook($book);

        $originalCategories = new ArrayCollection();
        foreach ($book->getCategories() as $category) {
            $categoryDto = CategoryDto::createFromCategory($category);
            $libroDto->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }

        $form = $this->createForm(LibroFormType::class, $libroDto);  
        $form -> handleRequest ($request);

        if (!$form -> isSubmitted()){

            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($form->isValid()) {

            // Remove categories
            foreach ($originalCategories as $originalCategoryDto) {
                if (!in_array($originalCategoryDto, $libroDto->categories)) {
                    $category = $categoryRepository->find($originalCategoryDto->id);
                    $book->removeCategory($category);
                }
            }

            // Add categories
            foreach ($libroDto->categories as $newCategoryDto) {
                if (!$originalCategories->contains($newCategoryDto)) {
                    $category = $categoryRepository->find($newCategoryDto->id ?? 0);
                    if (!$category) {
                        $category = new Category();
                        $category->setName($newCategoryDto->name);
                        $em->persist($category);
                    }
                    $book->addCategory($category);
                }
            }
            $book->setTitle($libroDto->title);
            if ($libroDto->base64Image) {
                $filename = $fileUploader->uploadBase64File($libroDto->base64Image);
                $book->setImage($filename);
            }

            $em->persist($book);
            $em->flush();
            return $book;
            // $this->bookManager->save($book);
            // $this->bookManager->reload($book);
            // return [$book, null];
        }
        // return [null, $form];

        return $form;
    }


        // [$book, $error] = ($bookFormProcessor)($book, $request);
        // $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        // $data = $book ?? $error;
        // return View::create($data, $statusCode);
}

