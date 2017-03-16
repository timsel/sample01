<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Law;
use AppBundle\Entity\Word;

use Doctrine\ORM\EntityNotFoundException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * 
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * List all words from repository
     * 
     * @Route("/allwords", name="allwords")
     */
    public function allWordsAction(Request $request)
    {
        try {
            $words = ($this->get('doctrine')
                ->getRepository('AppBundle:Word')
                ->findAll());

            $ret = [];
            /** @var Word $word */
            foreach ($words as $word) {
                $ret[] = $word->getWord();
            }

            return new JsonResponse($ret);
        } catch (\Exception $ex) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * List laws from repository by given word
     * 
     * @Route("/searchlawsbyword/{word}", name="searchlawsbyword")
     */
    public function searchLawsByWordAction(Request $request)
    {
        // check if word exists
        try {
            $word = ($this->get('doctrine')
                ->getRepository('AppBundle:Word')
                ->findOneBy([
                    'word' => $request->get('word')
                ]));

            if (!$word) {
                throw $this->createNotFoundException();
            }
        } catch (EntityNotFoundException $ex) {
            return new JsonResponse([], 404);
        } catch (\Exception $ex) {
            return new JsonResponse([], 500);
        }

        // get laws by word from repository
        try {
            $laws = ($this->get('doctrine')
                ->getRepository('AppBundle:LawWord')
                ->findByWord($word)
            );

            if (!$laws) {
                throw $this->createNotFoundException();
            }

            $ret = [];
            foreach ($laws as $law) {
                $ret[] = $law['url'];
            }

            return new JsonResponse($ret);
        } catch (EntityNotFoundException $ex) {
            return new JsonResponse([], 404);

        } catch (\Exception $ex) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * List word from repository by given law id
     * 
     * @Route("/searchwordsbylaw/{id}", name="searchwordsbylaw")
     */
    public function searchWordsByLawAction(Request $request)
    {
        // check if law exists
        try {
            /** @var Law $law */
            $law = ($this->get('doctrine')
                ->getRepository('AppBundle:Law')
                ->find($request->get('id')));

            if (!$law) {
                throw $this->createNotFoundException();
            }
        } catch (EntityNotFoundException $ex) {
            return new JsonResponse([], 404);
        } catch (\Exception $ex) {
            return new JsonResponse([], 500);
        }

        // get word by laws from repository
        try {
            $words = ($this->get('doctrine')
                ->getRepository('AppBundle:LawWord')
                ->findByLaw($law->getId())
            );

            if (!$words) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($words);
        } catch (EntityNotFoundException $ex) {
            return new JsonResponse([], 404);

        } catch (\Exception $ex) {
            return new JsonResponse([], 500);
        }

    }

    /**
     * List top10 words from repository
     * 
     * @Route("/top10", name="top10")
     */
    public function top10(Request $request)
    {
        // get top10 words from repository
        try {
            $words = ($this->get('doctrine')
                ->getRepository('AppBundle:LawWord')
                ->findTop10()
            );

            if (!$words) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($words);
        } catch (EntityNotFoundException $ex) {
            return new JsonResponse([], 404);

        } catch (\Exception $ex) {
            return new JsonResponse([], 500);
        }
    }
}