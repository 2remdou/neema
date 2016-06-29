<?php
/**
 * Created by PhpStorm.
 * User: touremamadou
 * Date: 14/06/2016
 * Time: 08:30
 */

namespace AppBundle\Repository;


trait UtilForRepository
{
    /**
     *
     * Ajoute des parametres à une requete dql, et retourne un arrayResult
     *
     * @param string $dql
     * @param array $paramters
     *
     * @return array
     */

    private function fillParameterAndGetResult($dql,$paramters=array()){
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameters($paramters);
        return $query->getArrayResult();
    }
    /**
     *
     * Ajoute des parametres à une requete dql, et retourne une cellule d'un arrayResult
     *
     * @param string $dql
     * @param array $paramters
     *
     * @return array
     */

    private function fillParameterAndGetSingleResult($dql,$paramters=array()){
        $resultat = $this->fillParameterAndGetResult($dql,$paramters);
        return count($resultat)===1?$resultat[0]:null;
    }
    /**
     *
     * Ajoute des parametres à une requete dql, et retourne une query
     *
     * @param string $dql
     * @param array $paramters
     *
     * @return array
     */

    private function fillParameterAndGetQuery($dql,$paramters=array()){
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameters($paramters);
        return $query;
    }

    /**
     *
     * Ajoute une ou des colonnes à une requête dql
     *
     * On recupère la liste des colonnes dans avant le FROM avec stristr
     *
     * Et on ajoute la ou les nouvelles colonnes, en prenant garde de mettre un espace à la fin pour les coller avec FROM
     *
     * @param string $dql
     * @param string $column
     *
     * @return string
     */

    private function addColumn($dql,$column){
        $oldSelect = stristr($dql,'FROM',true);
        $newDql = preg_replace('/'.$oldSelect.'/',$oldSelect.','.$column.' ',$dql);
        return $newDql;
    }
}