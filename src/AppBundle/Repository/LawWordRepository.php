<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * Class LawWordRepository
 * 
 * @package AppBundle\Repository
 */
class LawWordRepository extends EntityRepository
{

    /**
     * Returns laws by given word
     * 
     * @param $word_id
     * @return array|null
     */
    public function findByWord($word_id)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT l.url
                FROM AppBundle:LawWord lw
                LEFT JOIN AppBundle:Law l WITH lw.law_id = l.id
                WHERE lw.word_id = :id'
            )->setParameter('id', $word_id);

        try {
            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Returns words by given law
     * 
     * @param $law_id
     * @return array|null
     */
    public function findByLaw($law_id)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT w.word, lw.counter
                FROM AppBundle:LawWord lw
                LEFT JOIN AppBundle:Word w WITH lw.word_id = w.id
                WHERE lw.law_id = :id'
            )->setParameter('id', $law_id);

        try {
            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Returns top10 words
     * 
     * @return array|null
     */
    public function findTop10()
    {
        $query = $this->getEntityManager()
            ->createQuery(
                '
                SELECT
                  w.word,
                  SUM(lw.counter) AS counter
                FROM AppBundle:LawWord lw
                LEFT JOIN AppBundle:Word w WITH lw.word_id = w.id
                GROUP BY w.word
                ORDER BY lw.counter DESC
                '
            )->setMaxResults(10);

        try {
            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

}