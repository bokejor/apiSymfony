<?php

namespace App\Service;

use App\Entity\Libro;
use App\Form\Model\LibroDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\LibroFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class LibroFormProcessor
{

    private $libroManager;
    private $categoryManager;
    private $fileUploader;
    private $formFactory;

    public function __construct(
        LibroManager $libroManager,
        CategoryManager $categoryManager,
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory
    ) {
        $this->bookManager = $libroManager;
        $this->categoryManager = $categoryManager;
        $this->fileUploader = $fileUploader;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Libro $libro, Request $request): array
    {
        $libroDto = LibroDto::createFromBook($libro);
        $originalCategories = new ArrayCollection();
        foreach ($libro->getCategories() as $category) {
            $categoryDto = CategoryDto::createFromCategory($category);
            $libroDto->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }
        $form = $this->formFactory->create(LibroFormType::class, $libroDto);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if ($form->isValid()) {
            // Remove categories
            foreach ($originalCategories as $originalCategoryDto) {
                if (!in_array($originalCategoryDto, $libroDto->categories)) {
                    $category = $this->categoryManager->find($originalCategoryDto->id);
                    $libro->removeCategory($category);
                }
            }

            // Add categories
            foreach ($libroDto->categories as $newCategoryDto) {
                if (!$originalCategories->contains($newCategoryDto)) {
                    $category = $this->categoryManager->find($newCategoryDto->id ?? 0);
                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategoryDto->name);
                        $this->categoryManager->persist($category);
                    }
                    $libro->addCategory($category);
                }
            }
            $libro->setTitle($libroDto->title);
            if ($libroDto->base64Image) {
                $filename = $this->fileUploader->uploadBase64File($libroDto->base64Image);
                $libro->setImage($filename);
            }
            $this->bookManager->save($libro);
            $this->bookManager->reload($libro);
            return [$libro, null];
        }
        return [null, $form];
    }
}