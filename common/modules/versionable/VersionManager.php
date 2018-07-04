<?php
namespace common\modules\versionable;

use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Query;
use yii\db\Expression;

class VersionManager
{

    public function __construct()
    {
        
    }

    public static function getVersions(Versionable $res)
    {
        $query = new Query;
        $query->from(['v'=>$res->getResourceTable()])->where([$res->getResourceKey()=>$res->getResourceId()])->orderBy(['id' => SORT_DESC]);
        return $query->all();
    }



    public static function getLastVersions(Versionable $res)
    {
        $query = new Query;
        $query->from(['v'=>$res->getResourceTable()])->where([$res->getResourceKey()=>$res->getResourceId()])->limit(10)->orderBy(['id' => SORT_DESC]);
        return $query->all();
    }


    public static function getLastVersion(Versionable $res)
    {
        $query = new Query;
        $query->from(['v'=>$res->getResourceTable()])->where([$res->getResourceKey()=>$res->getResourceId()])->limit(1)->orderBy(['id' => SORT_DESC]);
        return $query->one();
    }
}