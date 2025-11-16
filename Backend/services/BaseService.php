<?php

abstract class BaseService {

    /**
     * @var mixed 
     */
    protected $dao;

    /**
     * Constructor for the BaseService.
     *
     * @param mixed 
     */
    public function __construct($dao) {
        $this->dao = $dao;
    }

}
?>
