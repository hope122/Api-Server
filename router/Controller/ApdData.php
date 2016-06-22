<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class ApdDataController
{
    public function indexAction()
    {
        $SysClass = new ctrlSystem;
        // 預設不連資料庫
        $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        // $SysClass->initialization(null,true);
        try{
            $action = array();
            $action["status"] = false;
            $action["msg"] = "請使用其他方法";
            $pageContent = $SysClass->Data2Json($action);
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("SupplyController", "editorAction", $error->getMessage());
        }
        //關閉資料庫連線
        // $SysClass->DBClose();
        //釋放
        $SysClass = null;
        $this->viewContnet['pageContent'] = $pageContent;
    }

    // 取得簽核資料的WF資料
    public function GetData_WorkFlowAction(){
        $SysClass = new ctrlSystem;
        // 預設不連資料庫
        // $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        $SysClass->initialization(null,true);
        try{
            $action = array();
            $action["status"] = false;
            if(!empty($_GET)){
                $wf_uid = $_GET["wf_uid"];
                $sys_code = $_GET["sys_code"];

                if($wf_uid and $sys_code){
                    // $strSQL = "select t2.*,t4.layer,t4.data_uid from apd_option t1 ";
                    // $strSQL .= "left join wf_option t2 on t1.wf_uid = t2.uid ";
                    // $strSQL .= "left join wf_title t3 on t3.wf_uid = t1.uid ";
                    // $strSQL .= "left join wf_layer_data t4 on t2.uid = t4.wf_uid ";
                    // $strSQL .= "where 1 ";

                    // $strSQL .= "and t1.wf_uid = '".$wf_uid."' "; 
                    // $strSQL .= "and t1.sys_code_id = '".$sys_code."' "; 
                    
                    // $strSQL .= "order by t1.uid asc ";
                    $strSQL = "select t1.*,t2.name as title,t3.layer,t3.data_uid,t5.name as orgName from wf_option t1 ";
                    $strSQL .= "left join wf_title t2 on t2.wf_uid = t1.uid ";
                    $strSQL .= "left join wf_layer_data t3 on t1.uid = t3.wf_uid ";
                    $strSQL .= "left join ass_org t4 on t3.data_uid = t4.uid ";
                    $strSQL .= "left join ass_type_office t5 on t4.officeid = t5.uid ";
                    $strSQL .= "where 1 ";

                    $strSQL .= "and t1.uid = '".$wf_uid."' "; 
                    $strSQL .= "and t1.sys_code_id = '".$sys_code."' "; 
                    
                    $strSQL .= "order by t1.uid asc ";
                    $data = $SysClass->QueryData($strSQL);

                    if(!empty($data)){
                        $action["data"] = $data;
                        $action["status"] = true;
                    }else{
                        $action["msg"] = '沒有資料';
                        // $action["SQL"] = $strSQL;
                    }
                }else{
                    $action["msg"] = 'wf_uid參數不可為空';
                }
            }else{
                $action["msg"] = '方法不正確';

            }
            $pageContent = $SysClass->Data2Json($action);
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("SupplyController", "editorAction", $error->getMessage());
        }
        //關閉資料庫連線
        // $SysClass->DBClose();
        //釋放
        $SysClass = null;
        $this->viewContnet['pageContent'] = $pageContent;
    }

    // 新增職務資料
    public function Insert_ApdDataAction(){
        $SysClass = new ctrlSystem;
        // 預設不連資料庫
        // $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        $SysClass->initialization(null,true);
        try{
            $action = array();
            $action["Status"] = false;

            if(!empty($_POST)){
                $doc_uid = $_POST["doc_uid"];
                $wfID = $_POST["wfID"];
                $userID = $_POST["userID"];
                $actionType = ($_POST["actionType"]) ? 1 : 0;
                $end_date = $_POST["end_date"];
                $sys_code = $_POST["sys_code"];

                if($doc_uid and $wfID and $userID and $end_date and $sys_code){
                    $strSQL = "select uid from wf_layer_data where wf_uid = '".$wfID."'";
                    $data = $SysClass->QueryData($strSQL);
                    if(!empty($data)){
                        $end_date = strtotime($end_date);
                        $start_date = time();
                        // print_r( $end_date . "\n");
                        // print_r( date("Y-m-d",$end_date) );
                        $SysClass->Transcation();
                        $strSQL = "insert into apd_option(doc_uid, wf_uid, sys_code_id, create_user, end_date, action_type, start_date) ";

                        $strSQL .= "values(".$doc_uid.", ".$wfID.", ".$sys_code.", ".$userID.", '".$end_date."', ".$actionType.", '".$start_date."'); ";
                        // echo $strSQL;
                        if($SysClass->Execute($strSQL)){
                            $insertStatus = true;
                            $newID = $SysClass->NewInsertID();
                            // $strSQL = "";
                            foreach ($data as $content) {
                                $strSQL = "insert into apd_data(apd_uid,wf_layer_id) ";
                                $strSQL .= "values(".$newID.", ".$content["uid"].");";
                                if(!$SysClass->Execute($strSQL)){
                                    $SysClass->Rollback();
                                    $insertStatus = false;
                                    break;
                                }
                            }
                            if($insertStatus){
                                $SysClass->Commit();
                                $action["msg"] = '新增成功';
                                $action["Data"] = $newID;
                                $action["Status"] = true;
                            }else{
                                $action["msg"] = '新增失敗';

                            }

                        }else{
                            $action["msg"] = '新增失敗';
                        }
                        

                    }else{
                        $action["msg"] = '無法查詢流程資料，新增失敗';
                    }

                
                }else{
                    $action["msg"] = '參數不可為空';

                }
            }else{
                $action["msg"] = '參數不可為空';

            }
            $pageContent = $SysClass->Data2Json($action);
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("SupplyController", "editorAction", $error->getMessage());
        }
        //關閉資料庫連線
        // $SysClass->DBClose();
        //釋放
        $SysClass = null;
        $this->viewContnet['pageContent'] = $pageContent;
    }

    // 修改自然人資料
    public function Update_ApdDataAction(){
        $SysClass = new ctrlSystem;
        // 預設不連資料庫
        // $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        $SysClass->initialization(null,true);
        try{
            $action = array();
            $action["Status"] = false;
            if(!empty($_POST)){
                
                $uid = $_POST["userID"];
                // 修改職務&部門
                $orgid = $_POST["orgid"];
                $posid = ($_POST["posid"]) ? $_POST["posid"]:"NULL";
                $sys_code_id = $_POST["sys_code_id"];
                
                if($uid and $orgid and $sys_code_id){
                    // 改部門方面的
                    $strSQL = "update ass_user set orgid='".$orgid."',posid=".$posid." ";
                    $strSQL .= "where uid='".$uid."'; ";

                    if($SysClass->Execute($strSQL)){
                        $action["Data"] = '修改成功';
                        $action["Status"] = true;

                    }else{
                        $action["msg"] = '修改失敗';
                    }
                }else{
                    $action["msg"] = '參數不可為空';

                }
            }else{
                $action["msg"] = '參數不可為空';

            }
            $pageContent = $SysClass->Data2Json($action);
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("SupplyController", "editorAction", $error->getMessage());
        }
        //關閉資料庫連線
        // $SysClass->DBClose();
        //釋放
        $SysClass = null;
        $this->viewContnet['pageContent'] = $pageContent;
    }

    //刪除自然人資料
    public function Delete_ApdDataAction()
    {
        $SysClass = new ctrlSystem;
        // 預設不連資料庫
        // $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        $SysClass->initialization(null,true);
        try{
            $action = array();
            $action["Status"] = false;
            // 先設置好全域變數以取得DELETE相關的部分
            $_DELETE = $SysClass->httpMethodVars();

            if(!empty($_DELETE)){
                $strSQL = "delete from ass_position where uid = '".$_DELETE["iUid"]."'";
                if($SysClass->Execute($strSQL)){
                    $action["Status"] = true;
                    $action["msg"] = '刪除成功';
                }else{
                    $action["msg"] = '刪除失敗';

                }
            }else{
                $action["msg"] = '沒有資料';
            }
            $pageContent = $SysClass->Data2Json($action);
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("SupplyController", "editorAction", $error->getMessage());
        }
        //關閉資料庫連線
        // $SysClass->DBClose();
        //釋放
        $SysClass = null;
        $this->viewContnet['pageContent'] = $pageContent;
    }

}
