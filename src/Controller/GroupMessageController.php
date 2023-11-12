<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupMessageController extends AbstractController
{
    #[Route('/group/message', name: 'app_group_message')]
    public function index(): Response
    {
        return $this->render('group_message/index.html.twig', [
            'controller_name' => 'GroupMessageController',
        ]);
    }
}
