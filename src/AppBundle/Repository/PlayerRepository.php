<?php

namespace AppBundle\Repository;

/**
 * ParticipantsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Renvoie tout les utilisateur d'une partie
     * @param $idGame
     * @return array|null
     */
    public function findPlayers($idGame){
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Player', 'p')
            ->where('g.id = :idGame')
            ->join('p.game', 'g')
            ->setParameter('idGame', $idGame)
            ->getQuery();

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * @param $idGame
     * @param $idUser
     */
    public function findPlayer($idGame, $idUser) {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Player', 'p')
            ->where('g.id = :idGame')
            ->andWhere('u.id = :idUser')
            ->join('p.user', 'u')
            ->join('p.game', 'g')
            ->setParameters(['idGame' => $idGame, 'idUser' => $idUser])
            ->getQuery();

        try{
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
