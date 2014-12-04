<?php

/*
 * add by xuye
 */

//实测暂时还不支持写扩展 *Model.class.php
class ExtendRelationshipCompanyLinkmanViewModel extends RelationshipCompanyLinkmanViewModel{
    protected $viewFields = array(
            "RelationshipCompanyLinkman"=> array('*', "_type"=>"left"),
            "RelationshipCompany" => array("name"=>"company_name", "_type"=>"left", "_on"=>"RelationshipCompany.id=RelationshipCompanyLinkman.relationship_company_id")，
            "User" => array("truename"=>"owner", "_on"=>"RelationshipCompany.user_id=User.id", "_type"=>"left")
    );
}
?>
