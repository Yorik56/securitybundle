<?php
// src/Controller/RegistrationController.php
namespace App\Controller;

use App\DB_Factory\DBFactory;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function index(UserPasswordHasherInterface $passwordHasher)
    {
        // ... e.g. get the user data from a registration form
        $user = new User();
        $plaintextPassword = "";

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        //GET DATABASE CONNEXION
        $db_mysql = DBFactory::create('mysql');
        $db_mysql_connexion = $db_mysql->connect([
            "HOSTNAME" => $this->getParameter('app.mysql_hostname'),
            "USERNAME" => $this->getParameter('app.mysql_username'),
            "PASSWORD" => $this->getParameter('app.mysql_password'),
            "DATABASE" => $this->getParameter('app.mysql_database')
        ]);

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // SAVE DATA
//            $stmt = $dbh->prepare("INSERT INTO user (email, password, roles) VALUES (:email, :password, :roles)");
//            $stmt->bindParam(':email', $user->getEmail());
//            $stmt->bindParam(':password', $user->getPassword());
//            $stmt->bindParam(':roles', $user->getRoles());
            $data = $db_mysql->query($db_mysql_connexion, 'INSERT INTO user ('.$user->getEmail().', '.$user->getPassword().', [\"ROLE_USER\"])');



            return $this->redirectToRoute('_profiler_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}