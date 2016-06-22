<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class workflowController
{
    public function getWorkFlowAction(){
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
                $sys_code = $_GET["sys_code"];
                $menu_code = $_GET["menu_code"];
                $uid = $_GET["uid"];
                $allData = $_GET["allData"];

                if($allData){
                    $strSQL = "select t1.*,t2.name,t4.name as userName from wf_option t1 ";
                }else{
                    $strSQL = "select t1.uid,t2.name from wf_option t1 ";
                }
                $strSQL .= "left join wf_title t2 on t1.uid = t2.wf_uid ";
                $strSQL .= "left join ass_user t3 on t1.creat_user = t3.uid ";
                $strSQL .= "left join ass_common t4 on t3.cmid = t4.uid ";
                // $strSQL .= "left join wf_layer_data t5 on t5.wf_uid = t1.uid ";
                $strSQL .= "where t1.sys_code_id = '".$sys_code."' and t1.menu_code = '".$menu_code."' ";
                if($uid){
                    $strSQL .= "and t1.uid = ".$uid." ";
                }
                $strSQL .= "order by t1.uid desc ";
                // $strSQL .= "group by t5.wf_uid ";

                $data = $SysClass->QueryData($strSQL);

                if(!empty($data)){
                    $action["data"] = $data;
                    $action["status"] = true;
                }else{
                    $action["msg"] = "無資料";
                    $action["sql"] = $strSQL;
                }
            }else{
                $action["msg"] = "參數不可為空";
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

    //work flow註冊
    public function insertWorkFlowAction()
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
            $action["status"] = false;

            if(!empty($_POST)){
                // 整理
                $layer = $_POST["layer"];
                $title = $_POST["title"];
                $menuCode = $_POST["menuCode"];
                $sysCode = $_POST["sysCode"];
                $user = $_POST["user"];
                
                // 新增流程名稱
                if($title and !empty($layer)){
                    // 創建流程設定
                    $strSQL = "insert into wf_option(creat_user, sys_code_id, menu_code) ";
                    $strSQL .= "values('".$user."','".$sysCode."','".$menuCode."');";
                    $SysClass->Execute($strSQL);
                    $wfID = $SysClass->NewInsertID();

                    $strSQL = "insert into wf_title(wf_uid,name) values('".$wfID."','".$title."');";
                    $SysClass->Execute($strSQL);

                    $strSQL = "";
                    // $wfCode = base64_encode(time());
                    // $wfCode = str_replace("==", "", $wfCode);
                    $layerNumber = 0;
                    foreach ($layer as $layerIndex => $value) {
                        if(!empty($value)){
                            $idArr = explode(",", $value);
                            foreach ($idArr as $idContent) {
                                $strSQL .= "insert into wf_layer_data(wf_uid,layer,data_uid,seq) ";
                                $strSQL .= "values('".$wfID."','".$layerNumber."','".$idContent."',0); ";
                            }
                            $layerNumber++;
                        }
                    }
                    // $action["sql"] = $strSQL;
                    if($SysClass->Execute($strSQL)){
                        $action["msg"] = "新增流程成功";
                        $action["data"] = $wfID;
                        $action["status"] = true;
                    }else{
                        $action["msg"] = "無法新增流程";
                    }
                }else{
                    $action["msg"] = "沒有輸入流程名稱";

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
    // 修改
    public function modifyAction()
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
            $action["status"] = false;

            if(!empty($_POST)){
                // 整理
                $layer = $_POST["layer"];
                $title = $_POST["title"];
                $menuCode = $_POST["menuCode"];
                $sysCode = $_POST["sysCode"];
                $user = $_POST["user"];
                $uid = $_POST["uid"];
                
                // 修改流程名稱
                if($title and !empty($layer) and $uid){
                    // 先找資料，如果和傳送過來的比對不符 => 砍掉
                    $strSQL = "delete from wf_layer_data where wf_uid = '".$uid."'";
                    if($SysClass->Execute($strSQL)){
                        // 砍掉再新增wf_layer_data
                        $strSQL = "";
                        $layerNumber = 0;
                        foreach ($layer as $layerIndex => $value) {
                            if(!empty($value)){
                                $idArr = explode(",", $value);
                                foreach ($idArr as $idContent) {
                                    $strSQL .= "insert into wf_layer_data(wf_uid,layer,data_uid,seq) ";
                                    $strSQL .= "values('".$uid."','".$layerNumber."','".$idContent."',0); ";
                                }
                                $layerNumber++;
                            }
                        }
                        $strSQL .= "update wf_title set name = '".$title."' where wf_uid = '".$uid."'; ";

                        if($SysClass->Execute($strSQL)){
                            $action["msg"] = "修改流程成功";
                            $action["status"] = true;
                        }else{
                            $action["msg"] = "無法修改流程";
                            // $action["sql"] = $strSQL;
                        }
                    }else{
                        $action["msg"] = "資料有誤，請重新傳送";
                    }
                }else{
                    $action["msg"] = "沒有輸入流程名稱";

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

    // 刪除
    public function deleteWFAction()
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
            $action["status"] = false;

            if(!empty($_POST)){
                // 整理
                $uid = $_POST["uid"];
                // 刪除流程
                if($uid){
                    // 先找資料，如果和傳送過來的比對不符 => 砍掉
                    $strSQL = "delete from wf_option where uid = '".$uid."'";
                    if($SysClass->Execute($strSQL)){
                        $action["msg"] = "刪除流程成功";
                        $action["status"] = true;
                    }else{
                        $action["msg"] = "資料有誤，請重新傳送";
                        $action["s"] = $strSQL;
                    }
                }else{
                    $action["msg"] = "沒有輸入流程名稱";

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
