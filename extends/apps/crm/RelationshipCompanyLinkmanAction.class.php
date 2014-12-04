<?php
/*
 * add by xuye
 */
class ExtendRelationshipCompanyLinkmanAction extends RelationshipCompanyLinkmanAction{

    //原方法应在扩展UserModel.class.php中，实测暂时还不支持*Model.class.php,因此在扩展action中实现该方法
    public function getChildrenDepartments($onlyId=true, $uid=null){
        $uid = (null != $uid) ? $uid : getCurrentUid();
        $model = D("Department");
        $userModel = D("User");
        $theUser = $userModel->find($uid);
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

    
    public function beforeFilter($model) {
        //搜索
        $map = array();
        $where = array();

        if($_GET["excludeId"]) {
            $map["id"] = array("NEQ", $_GET["excludeId"]);
        }

        if($_GET["_kw"] or $_GET["typeahead"]) {
            $kw = $_GET["_kw"] ? $_GET["_kw"] : $_GET["typeahead"];

            if($model->searchFields) {
                foreach($model->searchFields as $k => $sf) {
                    $where[$sf] = array('like', "%{$kw}%");
                }

            } else {
                $fields = array(
                    "name", "subject", "pinyin", "bill_id", "alias", "factory_code", "factory_code_all"
                );
                foreach($fields as $f) {
                    if($model->fields["_type"][$f]) {
                        $where[$f] = array('like', "%{$kw}%");
                    }
                }
            }

            if(count($where) > 1) {
                $where["_logic"] = "OR";
                $map["_complex"] = $where;
            } else {
                $map = array_merge_recursive($map, $where);
            }

        }

        //过滤器
        if($_GET["_filter_start_dateline"] && $_GET["_filter_end_dateline"]) {
            $map["dateline"] = array("BETWEEN", array(
                strtotime($_GET["_filter_start_dateline"]),
                strtotime($_GET["_filter_end_dateline"])
            ));
        }
        //工作流节点过滤器
        if($_GET["_filter_workflow_node"] && $this->workflowAlias) {
            $workflow = D("Workflow")->getByAlias($this->workflowAlias);
            $processMap = array(
                "workflow_id" => $workflow["id"],
                "node_id" => abs(intval($_GET["_filter_workflow_node"])),
                "status"  => 0
            );

            $inProcess = D("WorkflowProcess")->field("mainrow_id")->where($processMap)->select();

            if($inProcess) {
                foreach($inProcess as $p) {
                    $sourceIds[] = $p["mainrow_id"];
                }
                $map["id"] = array("IN", $sourceIds);
            } else {
                $this->response(array(
                    array("count" => 0, "totalPages"=>0), array()
                ));
                exit;
            }
        }
        //add by xuye  xysmiracle@hotmail.com
        //增加权限判断，只能看到自己部门和下属部门的信息
        $departmentIds = $this->getChildrenDepartments(true, getCurrentUid());
        $map["User.department_id"] = array("IN", $departmentIds); 

        //仅回收站数据
        if($_GET["onlyTrash"]) {
            $map["deleted"] = 1;
        }

        return $map;
    }

}

?>
