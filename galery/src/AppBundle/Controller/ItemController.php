<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Entity\User;
use AppBundle\Form\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/items")
 * Class ItemController
 * @package AppBundle\Controller
 */
class ItemController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

//    /**
//     * @Route("/create", name="create_item")
//     * @Method("GET")
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function createItem()
//    {
//        return $this->render("items/create.html.twig");
//    }
    /**
     * @Route("/create", name="create_item")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createItem(Request $request)
    {
        $item = new Item();

        $formOptions = array(
            'method' => 'POST',
            'action' => $this->generateUrl("create_item")
        );

        $form = $this->createForm(ItemType::class, $item, $formOptions);
        $form->add("submit", SubmitType::class);
        $form->handleRequest($request);

        if($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($item);

            $entityManager->flush();
//            return $this->render(":items:all.html.twig");
            return $this->redirectToRoute("print_items");
        }

        return $this->render("items/create.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="edit_form")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editItem(Request $request, $id)
    {
        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);
        if(!$item) {
            $this->redirectToRoute("create_item");
        }

        $formOptions = [
            "method" => "POST",
            "action" => $this->generateUrl("edit_form", ["id" => $id])
        ];

        $form = $this->createForm(ItemType::class, $item, $formOptions);
        $form->add("Delete", SubmitType::class);
        $form->add("Edit", SubmitType::class);
        $form->handleRequest($request);
//        var_dump($form);
//        exit;
        if($form->isSubmitted() && $form->isValid()) {
            if($form->get("Delete")->isClicked()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($item);
                $entityManager->flush();

                return $this->redirectToRoute("print_items");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->redirectToRoute("edit_form", ["id" => $id, "form" => $form->createView()]);
        }


        return $this->render("items/edit.html.twig", ["item" => $item, "form" => $form->createView()]);
    }

//    /**
//     * @Route("/create", name="create_item_process")
//     * @Method("POST")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function createItemProcess(Request $request)
//    {
//        $item = new Item();
//
//        $form = $this->createForm(ItemType::class, $item);
//        $form->handleRequest($request);
//
//        if($form->isValid()) {
//            echo "is valid";
//            exit;
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($item);
//
//            $entityManager->flush();
//            return $this->render(":items:all.html.twig");
//        }
//        echo "is not valid";
//        exit;
//
//        return $this->render(":items:create.html.twig");
//    }

    /**
     * @Route("/", name="print_items")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printAll()
    {
//        $repo = $this->getDoctrine()->getRepository(User::class);
//        $chields = $repo->find(5)->getChields();
//
//        foreach($chields as $chield) {
//            echo $chield->getUsername();
//            echo "<br>";
//        }
//        echo "<br>";
//        foreach($chields as $chield) {
//            echo $chield->getParent()->getUsername();
//            echo "<br>";
//        }
//        $chields[3]->setParent($this->getDoctrine()->getRepository(User::class)->find(4));
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->flush();
//        exit;



        $repo = $this->getDoctrine()->getRepository(Item::class);
        $items = $repo->findAll();

        return $this->render(":items:all.html.twig", ["items" => $items]);
    }

    /**
     * @Route("/id/{id}", name="print_item_by_id")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printItemById(Request $request, $id)
    {
        $repo = $this->getDoctrine()->getRepository(Item::class);
        $item = $repo->find($id);
//        $items = [$item];
        $items = $item?[$item]:[];
        return $this->render(":items:all.html.twig", ["items" => $items]);
    }

    /**
     * @Route("/name/{name}", name="print_item_by_name"))
     * @Method("GET")
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $name
     */
    public function printItemByName($name)
    {
        $repo = $this->getDoctrine()->getRepository(Item::class);
        $item = $repo->findBy(["name" => $name]);

//        var_dump($item);
//        echo "<br>";
//        var_dump($items);
//        exit;

        return $this->render(":items:all.html.twig", ["items" => $item]);
    }
}
//     * @Route("/{id}", name="print_item_by_id"), requirements={"id": "\d+"})