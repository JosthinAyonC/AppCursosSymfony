<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\Usuario;
use App\Form\CursoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/curso')]
class CursoController extends AbstractController
{
    #[Route('/', name: 'app_curso_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $cursos = $entityManager
            ->getRepository(Curso::class)
            ->findAll();

        return $this->render('curso/index.html.twig', [
            'cursos' => $cursos,
        ]);
    }

    #[Route('/new', name: 'app_curso_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_CREADOR')) {
            
            $user = $this->getUser();

            $cursoRepository = $entityManager->getRepository(Curso::class);

            $cursosCount = $cursoRepository->findBy(['idUsuario'=>$user, 'estado'=>'A']);
            if (count($cursosCount) > 1 ) {
                $errorMessage = 'No puedes crear más cursos, ya has creado 2 cursos.';
                $this->addFlash('error', $errorMessage);
                return $this->redirectToRoute('app_curso_index');
            }

            $curso = new Curso();
            $form = $this->createForm(CursoType::class, $curso);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                $curso->setIdUsuario($user);

                $entityManager->persist($curso);

                $estado = $form->get('estado')->getData();
                $curso->setEstado($estado);


                $entityManager->flush();

                return $this->redirectToRoute('app_curso_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('curso/new.html.twig', [
                'curso' => $curso,
                'form' => $form,
            ]);
        } else {
            return $this->render('usuario/accesDenied.html.twig');
        }
    }

    #[Route('/{idCurso}', name: 'app_curso_show', methods: ['GET'])]
    public function show(Curso $curso): Response
    {
        return $this->render('curso/show.html.twig', [
            'curso' => $curso,
        ]);
    }

    #[Route('/{idCurso}/edit', name: 'app_curso_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Curso $curso, EntityManagerInterface $entityManager): Response
    {

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_CREADOR')) {
            $form = $this->createForm(CursoType::class, $curso);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_curso_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('curso/edit.html.twig', [
                'curso' => $curso,
                'form' => $form,
            ]);
        } else {
            return $this->render('usuario/accesDenied.html.twig');
        }
    }

    #[Route('/{idCurso}', name: 'app_curso_delete', methods: ['POST'])]
    public function delete(Request $request, Curso $curso, EntityManagerInterface $entityManager): Response
    {

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_CREADOR')) {

            if ($this->isCsrfTokenValid('delete' . $curso->getIdCurso(), $request->request->get('_token'))) {
                $entityManager->remove($curso);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_curso_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->render('usuario/accesDenied.html.twig');
        }
    }
}
