<?php

namespace App\Controller;

use App\Entity\Suscripcion;
use App\Form\SuscripcionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suscripcion')]
class SuscripcionController extends AbstractController
{
    #[Route('/', name: 'app_suscripcion_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $suscripcions = $entityManager
            ->getRepository(Suscripcion::class)
            ->findBy(['idUsuario'=>$this->getUser()]);

        return $this->render('suscripcion/index.html.twig', [
            'suscripcions' => $suscripcions,
        ]);
    }

    #[Route('/new', name: 'app_suscripcion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CONSUMIDOR') || $this->isGranted('ROLE_ADMIN')) {
            $suscripcion = new Suscripcion();
            $form = $this->createForm(SuscripcionType::class, $suscripcion);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $suscripcion->setIdUsuario($this->getUser());
                
                
                $curso = $form->get('id_curso')->getData();
                $suscripcion->setIdCurso($curso);

                $entityManager->persist($suscripcion);
                $entityManager->flush();

                return $this->redirectToRoute('app_suscripcion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('suscripcion/new.html.twig', [
                'suscripcion' => $suscripcion,
                'form' => $form,
            ]);
        }else {
            return $this->render('usuario/accesDenied.html.twig');
        }
    }

    #[Route('/{idUsuario}', name: 'app_suscripcion_show', methods: ['GET'])]
    public function show(Suscripcion $suscripcion): Response
    {
        return $this->render('suscripcion/show.html.twig', [
            'suscripcion' => $suscripcion,
        ]);
    }

    #[Route('/{idUsuario}/edit', name: 'app_suscripcion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Suscripcion $suscripcion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuscripcionType::class, $suscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_suscripcion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suscripcion/edit.html.twig', [
            'suscripcion' => $suscripcion,
            'form' => $form,
        ]);
    }

    #[Route('/{idUsuario}', name: 'app_suscripcion_delete', methods: ['POST'])]
    public function delete(Request $request, Suscripcion $suscripcion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $suscripcion->getIdUsuario(), $request->request->get('_token'))) {
            $entityManager->remove($suscripcion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_suscripcion_index', [], Response::HTTP_SEE_OTHER);
    }
}
