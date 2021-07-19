<?php

namespace App\Controller;

use App\Entity\Constructeur;
use App\Form\ConstructeurType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConstructeurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConstructeurController extends AbstractController
{
    /**
     * @Route("/constructeur", name="constructeur")
     */
    public function index(ConstructeurRepository $repository): Response
    {
        $constructeurs = $repository->findAll();
        return $this->render('constructeur/index.html.twig', [
            'constructeurs' => $constructeurs,
        ]);
    }
    /**
     * @Route("/showConstructeur/{id<\d+>}", name="showConstructeur")
     */
    public function show(Constructeur $constructeur): Response{

        return $this->render('constructeur/show.html.twig', [
            'constructeur' => $constructeur,
        ]);

    }
    /**
     *
     * @Route("/deleteConstructeur/{id}", name="deleteConstructeur")
     */
    public function delete(Constructeur $constructeur, EntityManagerInterface $manager): Response
    {
        $manager->remove($constructeur);
        $manager->flush();
        return $this->redirect('/constructeur');
    }
    /**
     * @Route("/addConstructeur", name="addConstructeur")
     * @Route("/editConstructeur/{id}", name="editConstructeur")
     */
    public function add(Constructeur $constructeur = null, Request $requete, EntityManagerInterface $manager):Response
    {

        if(!$constructeur){
            $constructeur = new constructeur;
            $modeEdition =false;
        }
        else{
            $modeEdition= true;
        }

        $formConstructeur = $this->createForm(ConstructeurType::class, $constructeur);
        $formConstructeur->handleRequest($requete);

        if($formConstructeur->isSubmitted() && $formConstructeur->isValid())
        {
            $img= $formConstructeur->get('imageLogo')->getData();

            if($img)
            {
                try
                {
                    $nomImg = uniqid().".".$img->guessExtension();
                    $img->move($this->getParameter('constructeurs_images'), $nomImg);
  
                }catch(FileException $e)
                {
                    throw $e;
                    //return $this->redirectToRoute('telephone');
                }
            }
            if(!$modeEdition||($modeEdition && $img)){

                $constructeur->setImageLogo($nomImg);
                

            }
            $manager->persist($constructeur);
            $manager->flush();
            if($modeEdition){
                return $this->redirectToRoute('show', [
                    'id' => $constructeur->getId(),
                ]);
            }
            return $this->redirectToRoute('constructeur');
        }
        return $this->render('constructeur/add.html.twig', ['formConstructeur'=> $formConstructeur->createView(), 'constructeur'=> $constructeur, 'modeEdition'=> $modeEdition
            
        ]);
    }
}

