<?php

namespace Phpbr\AppBundle\Controller\Forum;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Phpbr\AppBundle\Entity\Forum\Topico;
use Phpbr\AppBundle\Entity\Forum\Categoria;
use Phpbr\AppBundle\Form\Type\ForumTopicoFormType;

class TopicoController extends Controller
{
    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $topicos = $em->getRepository('PhpbrAppBundle:Forum\Topico')
            ->find($id);

        return $this->render('PhpbrAppBundle:Forum:ver.html.twig', array(

        ));
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verTopicoAction($id)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();

        $categoria = $em->getRepository('PhpbrAppBundle:Forum\Categoria')
            ->find($id);

        $topicos = $categoria->getTopicos();
        $session->set('forumCategoria', $categoria->getId());

        return $this->render('@PhpbrApp/Forum/ver.html.twig', array(
            'categoria' => $categoria,
            'topicos'   => $topicos
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function novoAction(Request $request)
    {
        $topico = new Topico();
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $categoriaId = $session->get('forumCategoria');
        $emCategoria = $this->getDoctrine()->getManager();
        $categoria = $emCategoria->getRepository('PhpbrAppBundle:Forum\Categoria')
            ->find($categoriaId);

        $usuario = $this->get('security.context')->getToken()->getUser();

        $form = $this->createForm(new ForumTopicoFormType(), $topico, array());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $topico->setUser($usuario);

            $topico->setCategoria($categoria);
            $topico->setDataCriacao(new \DateTime());

            $em->persist($topico);
            $em->flush();

            return $this->redirect($this->generateUrl('forum_ver_mensagem',
                array(
                    'slug_categoria' => $categoria->getSlug(),
                    'slug' => $topico->getSlug()
                )
            ));
        }

        return $this->render('PhpbrAppBundle:Forum:novo.html.twig', array(
            'form' => $form->createView(),
            'categoria' => $categoria
        ));
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletarAction($id)
    {
        return $this->render('PhpbrAppBundle:Forum:deletar.html.twig', array(
                // ...
        ));    
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletarTopicoAction($id)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $topico = $em->getRepository('PhpbrAppBundle:Forum\Topico')
            ->findOneBy(
                array('id' => $id)
            );

        if (!$topico) {
            throw $this->createNotFoundException('Nao achou este topico!');
        }


        if ($topico->getUser() != $usuario){
            throw $this->createNotFoundException('Negado!');
        }

        $categoria = $topico->getCategoria();

        $em->remove($topico);
        $em->flush();

        $this->addFlash(
            'notice',
            'Topico deletado com sucesso!'
        );

        return $this->redirect(
            $this->generateUrl(
                'forum_ver_categoria',
                array(
                    'slug' => $categoria->getSlug()
                )
            )
        );
    }
}

