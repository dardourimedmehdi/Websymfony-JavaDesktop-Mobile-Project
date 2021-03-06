<?php

namespace EvenementBundle\Controller;

use EvenementBundle\Entity\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Evenement/Default/index.html.twig');
    }
    public function alleventAction()
    {
        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('EvenementBundle:Evenement')
            ->findAll();
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted  = $serializer->normalize($tasks);
        return new JsonResponse($formatted);
    }


    public function allinscritAction()
    {
        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('EvenementBundle:Inscription')
            ->findAll();
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        //$formatted = $serializer->normalize($tasks);
        $formatted = $serializer->normalize($tasks,'json', [AbstractNormalizer::ATTRIBUTES => ['id','idevent'=>['id'],'iduser'=>['id']]]);
        return new JsonResponse($formatted);
    }


    public function inscritAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $ev = $this->getDoctrine()->getManager()
            ->getRepository('EvenementBundle:Evenement')
            ->find($request->get('event'));
        $u = $this->getDoctrine()->getManager()
            ->getRepository('MainBundle:User')
            ->find($request->get('user'));


        $nbr = $ev->getNbrplace();
        $nbr = $nbr-1;
        $ev->setNbrplace($nbr);


        $inscription = new Inscription();

        $inscription->setIdevent($ev);
        $inscription->setIduser($u);


        $em->persist($inscription);
        $em->flush();
/*
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($inscription);
*/
        return new JsonResponse("add action api");
    }




    public function AinscritAction(Request $request)
    {

        $commentaires = $this->getDoctrine()->getManager()
            ->getRepository('EvenementBundle:Inscription')
            ->findOneBy(array('idevent'=>$request->get("event"),'iduser'=>$request->get("user")));

        if($commentaires)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentaires);
            $em->flush();
        }

/*
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($commentaires,'json', [AbstractNormalizer::ATTRIBUTES => ['id','iduser'=>['id'],'idevent'=>['id']]]);
*/
        return new JsonResponse("delete action api");


    }




    public function ideventAction(Request $request)
    {
        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('EvenementBundle:Evenement')
            ->find($request->get('id'));
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted  = $serializer->normalize($tasks);
        return new JsonResponse($formatted);
    }


    public function TypetriAction()
    {
        $tasks = $this->getDoctrine()->getManager()->getRepository('EvenementBundle:Evenement')
            ->createQueryBuilder('e')
            ->addOrderBy('e.type', 'ASC')
            ->getQuery()
            ->execute();
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted  = $serializer->normalize($tasks);
        return new JsonResponse($formatted);
    }
}
