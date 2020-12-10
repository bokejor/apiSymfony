<?php

namespace App\Service;

use App\Entity\Libro;
use App\Repository\LibroRepository;
use Doctrine\ORM\EntityManagerInterface;

class LibroManager
{

    private $em;
    private $libroRepository;

    public function __construct(EntityManagerInterface $em, LibroRepository $libroRepository)
    {
        $this->em = $em;
        $this->libroRepository = $libroRepository;
    }

    public function find(int $id): ?Libro
    {
        return $this->libroRepository->find($id);
    }

    public function getRepository(): LibroRepository
    {
        return $this->libroRepository;
    }

    public function create(): Libro
    {
        $libro = new Libro();
        return $libro;
    }

    public function save(Libro $libro): Libro
    {
        $this->em->persist($libro);
        $this->em->flush();
        return $libro;
    }

    public function reload(Libro $libro): Libro
    {
        $this->em->refresh($libro);
        return $libro;
    }

    public function delete(Libro $libro)
    {
        $this->em->remove($libro);
        $this->em->flush();
    }
}