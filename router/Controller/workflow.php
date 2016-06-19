<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class workflowController
{

    //客戶資料註冊
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
                $strSQL = "";
                $wfCode = base64_encode(time());
                $wfCode = str_replace("==", "", $wfCode);
                foreach ($layer as $layerIndex => $value) {
                    $idArr = explode(",", $value);
                    foreach ($idArr as $idContent) {
                        $strSQL .= "insert into wf_option(title,layer,data_uid,creat_user,sys_code,menu_code,seq,layer_seq,wf_code) ";
                        $strSQL .= "values('".$title."','".$layerIndex."','".$idContent."','".$user."','".$sysCode."','".$menuCode."','0','0','".$wfCode."'); ";
                    }
                }
                if($SysClass->Execute($strSQL)){
                    $action["msg"] = "新增流程成功";
                    $action["status"] = true;
                }else{
                    $action["msg"] = "無法新增流程";
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
