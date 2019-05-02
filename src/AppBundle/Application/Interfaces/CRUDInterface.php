<?php


namespace AppBundle\Application\Interfaces;


use Symfony\Component\HttpFoundation\Request;

/**
 * Interface CRUDInterface
 * @package AppBundle\Application\Interfaces
 */
interface CRUDInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function fetch(Request $request);

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request);

    /**
     * @param $object
     * @param Request $request
     * @return mixed
     */
    public function update($object, Request $request);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);
}