<?php
namespace App\Controller;


use App\Entity\Animals;
use App\Form\AnimalType;

use App\Form\UpdateUserType;
use App\Repository\AnimalsRepository;
use App\Repository\DoctorsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ProfileController extends Controller
{
    public function addAnimal(Request $request, AnimalsRepository $anr)
    {
        $error = null;
        $animal= new Animals();
        $form = $this->createForm(AnimalType::class, $animal);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $animal->setOwner($this->getUser());
            $error = $anr->addAnimal($animal);

            if($error==null){
                return $this->redirectToRoute('userProfile');
            }else{
                $form->addError(new FormError('Exception: '.$error));
            }
        }

        return $this->render('addAnimal.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function getPatient(Request $request, UsersRepository $ur)
    {
        $userID=$request->get('id');
        if(isset($userID)){
            $user = $ur->findByDni($userID);
        }else{
            return $this->redirectToRoute('index');
        }
        return $this->render('userProfile.html.twig', array(
            'user' => $user,
        ));
    }

    public function subscribe(Request $request, UsersRepository $ur)
    {
        $creditCard=$request->get('cc');
        if(isset($creditCard)){
            $user = $this->getUser();
            $user->setCreditCard($creditCard);
            $user->setIsSubscribed(true);
            $ur->update($user);
            return $this->redirectToRoute('account');
        }
        return $this->render('subscribe.html.twig');
    }
    public function getDoctor(Request $request, DoctorsRepository $dr)
    {
        $userID=$request->get('id');
        if(isset($userID)){
            $user = $dr->findByDni($userID);
        }else{
            return $this->redirectToRoute('index');
        }
        return $this->render('doctorProfile.html.twig', array(
            'doctor' => $user,
        ));
    }
    public function getAccount(AnimalsRepository $anr){
        $user = $this->getUser();
        if($this->isGranted('ROLE_DOCTOR')){
            return $this->render('doctorProfile.html.twig', array(
                'doctor' => $user,
            ));
        }elseif($this->isGranted('ROLE_USER')){
            $animals = $anr->findByOwner($user);
            return $this->render('userProfile.html.twig', array(
                'user' => $user,
                'pets' => $animals,
            ));
        }
        return $this->redirectToRoute('index');
    }
    public function update(Request $request, UsersRepository $ur, UserPasswordEncoderInterface $passwordEncoder)
    {
        $error = null;
        $user= $this->getUser();
        $uuser= clone $user;
        $form = $this->createForm(UpdateUserType::class, $uuser);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($uuser->getPlainPassword())
                $password = $uuser->getPlainPassword();
                $birth = $uuser->getBirthdate();
                $phone = $uuser->getPhone();
                $ssn = $uuser->getSocialSecurityNumber();
                $name = $uuser->getUsername();

                if(isset($password)){
                    $user->setPassword($passwordEncoder->encodePassword($uuser, $password));
                }
                if(isset($birth)){
                    $user->setBirthdate($birth);
                }
                if(isset($phone)){
                    $user->setPhone($phone);
                }
                if(isset($ssn)){
                    $user->setSocialSecurityNumber($ssn);

                }
                if(isset($name)){
                    $user->setUsername($name);

                }
            $error = $ur->update($user);

            if($error==null){
                return $this->redirectToRoute('account');
            }else{
                $form->addError(new FormError('Exception: '.$error));
            }
        }

        return $this->render('updateUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }


}