<?php
namespace common\modules\versionable;

interface Versionable
{   

    public static function versionableAttributes();

    public  function getVersionableAttributes();

    /**
     * @return int
     */
    public function getCurrentVersion();

    /**
     * @return int
     */
    public function getLastVersion();
   
    /**
     * @return boolian
     */
    public function setCurrentVersion($v);

    
    public static function resourceKey();


    public static function getPrimaryKeyTitle();

    /**
     * @return string
     */
    public static function resourceTableName();


    /**
     * @return string
     */
    public function getResourceKey();


    /**
     * @return int
     */
    public  function getResourceId();


    /**
     * @return string
     */
    public  function getResourceTable();

    /**
     * @return boolian
     */
    public  function saveHistory($defaultAttr = array());
}