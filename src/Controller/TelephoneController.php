<?php

namespace App\Controller;

use App\Entity\Telephone;
use App\Form\TelephoneType;
use App\Repository\TelephoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class TelephoneController extends AbstractController
{
    /**
     * @Route("/telephone", name="telephone")
     */
    public function index(TelephoneRepository $repository): Response
    {
        $telephones = $repository->findAll();

        return $this->render('telephone/index.html.twig', [
            'telephones' => $telephones,
        ]);
    }
    /**
     * @Route("/show/{id<\d+>}", name="show")
     */
    public function show(Telephone $telephone): Response{

        return $this->render('telephone/show.html.twig', [
            'telephone' => $telephone,
        ]);

    }
    /**
     * @Route("/add", name="add")
     * @Route("/edit/{id}", name="edit")
     */
    public function add(Telephone $telephone = null, Request $requete, EntityManagerInterface $manager):Response
    {

        if(!$telephone){
            $telephone = new telephone;
            $modeEdition =false;
        }
        else{
            $modeEdition= true;
        }

        $formTelephone = $this->createForm(TelephoneType::class, $telephone);
        $formTelephone->handleRequest($requete);

        if($formTelephone->isSubmitted() && $formTelephone->isValid())
        {
            $img= $formTelephone->get('image')->getData();

            if($img)
            {
                try
                {
                    $nomImg = uniqid().".".$img->guessExtension();
                    $img->move($this->getParameter('telephones_images'), $nomImg);
  
                }catch(FileException $e)
                {
                    throw $e;
                    //return $this->redirectToRoute('telephone');
                }
            }
            if(!$modeEdition||($modeEdition && $img)){

                $telephone->setImage($nomImg);
                $telephone->setCreatedDate(new \DateTime());

            }
            $manager->persist($telephone);
            $manager->flush();
            if($modeEdition){
                return $this->redirectToRoute('show', [
                    'id' => $telephone->getId(),
                ]);
            }
            return $this->redirectToRoute('telephone');
        }
        return $this->render('telephone/add.html.twig', ['formTelephone'=> $formTelephone->createView(), 'telephone'=> $telephone, 'modeEdition'=> $modeEdition
            
        ]);
    }
    /**
     *
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Telephone $telephone, EntityManagerInterface $manager): Response
    {
        $manager->remove($telephone);
        $manager->flush();
        return $this->redirect('/telephone');
    }
}
