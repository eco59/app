<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmployeController extends AbstractController
{
    #[Route(path: '/employee', name: 'app_employee')]
    public function employee_espace(): Response
    {
        return $this->render('employe/espace_employe.html.twig');
    }
}