<?php

namespace AppBundle\Command;

use AppBundle\Entity\Law;
use AppBundle\Entity\LawWord;
use AppBundle\Entity\Word;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParseCommand
 * 
 * @package AppBundle\Command
 */
class ParseCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:parse');
    }

    /**
     * Setup entity manager for class
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->output = $output;
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processLaws();
        $this->processWords();
    }

    /**
     * Parse laws listed in config
     * 
     * @throws \Exception
     */
    protected function processLaws()
    {
        // get urls from config
        $lawUrls = $this->getContainer()->getParameter('law_urls');

        // find laws by urls
        $laws = $this->getContainer()->get('doctrine')
            ->getRepository('AppBundle:Law')
            ->findBy([
                'url' => $lawUrls
            ]);

        $tempLaws = [];
        /** @var Law $law */
        foreach ($laws as $law) {
            $tempLaws[] = $law->getUrl();
        }

        // get new laws
        $newLaws = array_diff($lawUrls, $tempLaws);
        
        if (!empty($newLaws)) {
            foreach ($newLaws as $url) {

                // stores the Law in database
                $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                try {
                    $law = new Law();
                    $law->setUrl($url);
                    $this->em->persist($law);
                    $this->em->flush();
                    $this->em->getConnection()->commit();
                } catch (\Exception $e) {
                    $this->em->getConnection()->rollBack();
                    // todo: log
                    throw $e;
                }

            }
        }
    }

    /**
     * Process words from laws
     * 
     * @throws \Exception
     */
    protected function processWords()
    {
        // find all laws
        $laws = $this->getContainer()->get('doctrine')
            ->getRepository('AppBundle:Law')
            ->findAll();

        // prepare a working array with words / laws / counters
        /** @var Law $law */
        $words = [];
        foreach ($laws as $law) {
            $tempWords = preg_split('/\s+/', $this->parseHtml($law->getUrl()));
            foreach ($tempWords as $word) {
                $words[$word][$law->getId()] = isset($words[$word][$law->getId()]) ? ++$words[$word][$law->getId()] : 1;
            }
        }
        
        // store new words in database
        foreach ($words as $str => $data) {
            $word = $this->getContainer()->get('doctrine')
                ->getRepository('AppBundle:Word')
                ->findOneBy([
                    'word' => $str
                ]);

            if (empty($word)) {
                try {
                    $word = new Word();
                    $word->setWord($str);
                    $this->em->getConnection()->beginTransaction(); // suspend auto-commit
                    $this->em->persist($word);
                    $this->em->flush();
                    $this->em->getConnection()->commit();
                } catch (\Exception $e) {
                    $this->em->getConnection()->rollBack();
                    // todo: log
                    throw $e;
                }
            }

            // call processRelations with collected data / by word
            $this->processRelations($word, $data);
        }
    }

    /**
     * Store new relations in database
     * 
     * @param Word $word
     * @param array $data
     */
    protected function processRelations(Word $word, array $data)
    {
        // get relations from repository
        $relations = $this->getContainer()->get('doctrine')
            ->getRepository('AppBundle:LawWord')
            ->findBy([
                'word_id' => $word->getId(),
                'law_id' => array_keys($data)
            ]);

        // get new relations
        $diffRelations = [];
        if (!empty($relations)) {
            foreach ($relations as $relation) {
                $diffRelations[] = $relation->getLawId();
            }
        }
        $diff = array_diff(array_keys($data), $diffRelations);

        // store new relations in database
        foreach ($diff as $lawId) {
            $lawWord = new LawWord();
            $lawWord->setWordId($word->getId());
            $lawWord->setLawId($lawId);
            $lawWord->setCounter($data[$lawId]);
            $this->em->persist($lawWord);
            $this->em->flush();
        }
    }

    /**
     * Very, very simple html2text implementation
     * 
     * @param $url
     * @return mixed|string
     */
    protected function parseHtml($url)
    {
        $content = strtolower(strip_tags(file_get_contents($url)));
        $content = str_replace(
            ['.', ',', ';', ':', '"', "'", '!', '?', '=', '-', '(', ')', '{', '}'],
            [''],
            $content
        );

        return $content;
    }

}
