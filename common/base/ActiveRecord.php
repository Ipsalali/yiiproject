<?php

namespace common\base;


class ActiveRecord extends \yii\db\ActiveRecord{


	/**
    * @param @attr = attribute name
    * @return translate attribute labels
    */
    public function getAttributeLabel($attr){
        $dic = (new \ReflectionClass($this))->getShortName();
        return \Yii::t($dic,$attr);
    }


    public function getAttributeHint($attr)
    {	
    	$dic = (new \ReflectionClass($this))->getShortName();
        $hints = $this->attributeHints();

        $hint = $attr."_hint";
        $lang_hint = \Yii::t($dic,$hint);
        $hint = $hint === $lang_hint ? "" : $lang_hint;
        
        return isset($hints[$attr]) ? $hints[$attr] : $hint;
    }
}

?>