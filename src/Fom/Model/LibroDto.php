<?php

namespace App\Form\Model;

use App\Entity\Libro;

class LibroDto
{
    public $title;
    public $base64Image;
    // public $categories;

    // public function __construct()
    // {
    //     $this->categories = [];
    // }

    // public static function createFromBook(Book $book): self
    // {
    //     $dto = new self();
    //     $dto->title = $book->getTitle();
    //     return $dto;
    // }
}