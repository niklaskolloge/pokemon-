<?php

namespace App\Controller;

use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\httpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokemonController extends Controller
{
    /**
     * @Route(path="/ersteseite", name="pokemonerste_index")
     * @return Response
     */
    public function indexAction(EntityManagerInterface $entityManager): Response
    {
        $name = 'auf der ersten Seite';

        $pokemon = new Pokemon();
        $pokemon->setId(1);
        $pokemon->setName("Bisasam");
        $pokemon->setHeight(20);
        $pokemon->setWeight(30);
        $pokemon->setImage("bild");

        $entityManager->persist($pokemon);
        $entityManager->flush();

        return $this->render('pages/index.html.twig', ['name' => $name, 'pokemon' => $pokemon]);
    }

    /**
     * @Route(path="/zweiteseite", name="pokemonzweite_index")
     * @return Response
     */
    public function indexTwoAction(EntityManagerInterface $entityManager): Response
    {

        $repository = $entityManager->getRepository(Pokemon::class);
        $pokemon = $repository->find(1);

        $name = 'auf der zweiten Seite';
        return $this->render('pages/index.html.twig', ['name' => $name, 'pokemon' => $pokemon]);
    }

    /**
     * @Route(path="/details/{id}", name="pokemondetails")
     * @return Response
     */
    public function details_action(int $id, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $entityManager->getRepository(Pokemon::class)->find($id);

        if ($pokemon === null) {
            try {
                $apiResponse = json_decode(file_get_contents("http://pokeapi.co/api/v2/pokemon/$id"), true);

                $pokemon = new Pokemon();
                $pokemon->setId($apiResponse['id']);
                $pokemon->setName($apiResponse['name']);
                $pokemon->setImage($apiResponse['sprites']['front_default']);
                $pokemon->setWeight($apiResponse['weight']);
                $pokemon->setHeight($apiResponse['height']);

                $entityManager->persist($pokemon);
                $entityManager->flush();
            } catch (\Exception $e) {
                return new Response("No Pokemon found for this ID");
            }
        }

        return $this->render('pages/details.html.twig', ['pokemon' => $pokemon]);
    }

}