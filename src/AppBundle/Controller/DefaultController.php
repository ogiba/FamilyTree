<?php

namespace AppBundle\Controller;

use AppBundle\Model\About;
use AppBundle\Model\PostPage;
use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        $postsPage = $this->getPosts(5,1);

        $aboutInfo = new About(" Quisque pharetra, urna mattis sed, posuere sit amet dui turpis dolor, porttitor
                                    odio.
                                    Nunc condimentum vitae, dapibus vitae, vestibulum ac, auctor mi. Curabitur eget
                                    imperdiet sagittis, nunc iaculis malesuada fames ac lectus. Ut sodales felis, in
                                    vehicula est. In hac habitasse platea dictumst. Proin orci. Integer egestas, dui
                                    dui,");

        $request->setLocale("pl");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            "postsPage" => $postsPage,
            "aboutInfo" => $aboutInfo
        ));
    }

    /**
     * @Route("/{page}")
     * @Method({"GET"})
     */
    public function postPageAction($page){

        $postsPage = $this->getPosts(5,$page);

        return $this->render(":default:posts.html.twig", array(
            "postsPage" => $postsPage
        ));
    }

    private function getPosts($pageSize, $pageNumber){
        $postsPage = new PostPage();

        $postPageSize = $pageSize != null && $pageSize > 0 ? $pageSize : 10;

        $totalNumberOfItems = 20;
        $totalNumberOfPages = ceil($totalNumberOfItems / $postPageSize);

        $postsPage->setTotalItems($totalNumberOfItems);

        $selectedPageNumber = $pageNumber != null && $pageNumber > 0 && $pageNumber <= $totalNumberOfPages ? $pageNumber : 1;

        $postsPage->setNumberOfPages($totalNumberOfPages);

        $postsPage->setCurrentPage($selectedPageNumber);

        $ps = array();
        for ($i = 0; $i < $postPageSize; $i++) {
            $ps[$i] = "$i $pageNumber";
        }
        $postsPage->setPosts($ps);

        return $postsPage;
    }

    private function setupSerializer() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer;
    }
}
