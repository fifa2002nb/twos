<?php

/**
  * @filename UserModel.class.php
  * @encoding UTF-8
  * @author xuye
  */
class ExtendUserModel extends UserModel{
    //实测暂时还不支持写扩展 *Model.class.php，因此暂时在各个扩展*Action.class.php中实现
    public function getChildrenDepartments($onlyId=true, $uid=null){
        $uid = (null != $uid) ? $uid : getCurrentUid();
        $model = D("Department");
        $theUser = $this->find($uid);
        $depId = $theUser["department_id"];
        $tmp = $model->getTree($depId);
        $departments = array();
        if($onlyId)
            foreach($tmp as $k=>$t) {
                $departments[] = $t["id"];
            }
        else
            foreach($tmp as $k=>$t) {
                $departments[] = $t;
            }
        return $departments;
    }
}

?>
