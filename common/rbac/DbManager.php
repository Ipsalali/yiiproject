<?php

namespace common\rbac;

use Yii;
use yii\rbac\DbManager as yDbManager;
use yii\base\InvalidArgumentException;
use yii\base\InvalidCallException;
use yii\caching\CacheInterface;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\di\Instance;


class DbManager extends yDbManager{

	private $_checkAccessAssignments = [];

    /**
     * {@inheritdoc}
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        if (isset($this->_checkAccessAssignments[(string) $userId])) {
            $assignments = $this->_checkAccessAssignments[(string) $userId];
        } else {
            $assignments = $this->getUserRights($userId);
            $this->_checkAccessAssignments[(string) $userId] = $assignments;
        }


        if ($this->hasNoAssignments($assignments)) {
            return false;
        }

        $permissionName = strtolower($permissionName);
        $this->loadFromCache();
        if ($this->items !== null) {
            return $this->checkAccessFromCache($userId, $permissionName, $params, $assignments);
        }

        return (isset($assignments[$permissionName]) || in_array($permissionName, $this->defaultRoles));
        //Убираем рекурсивную проверку прав
        //return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
    }



    public function getUserRights($userId){

        if ($this->isEmptyUserId($userId)) {
            return [];
        }

    	$sql = "CALL get_UserRights($userId)";
    	$items = Yii::$app->db->createCommand($sql)->queryAll();

    	$rights = array();
    	if(count($items)){
    		foreach ($items as $i) {
    			$rights[$i['right']] = $i;
    		}
    	}

    	return $rights;
    }

    /**
     * Check whether $userId is empty.
     * @param mixed $userId
     * @return bool
     */
    private function isEmptyUserId($userId)
    {
        return !isset($userId) || $userId === '';
    }
}

?>