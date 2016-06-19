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
                
                // 新增流程名稱
                if($title){
                    $strSQL = "insert into wf_title(name) values('".$title."');";
                    $SysClass->Execute($strSQL);

                    $titleID = $SysClass->NewInsertID();
                    // 創建流程設定
                    $strSQL = "insert into wf_option(title_id , creat_user, sys_code_id, menu_code) ";
                    $strSQL .= "values('".$titleID."','".$user."','".$sysCode."','".$menuCode."');";
                    $SysClass->Execute($strSQL);
                    $wfID = $SysClass->NewInsertID();

                    $strSQL = "";
                    // $wfCode = base64_encode(time());
                    // $wfCode = str_replace("==", "", $wfCode);
                    foreach ($layer as $layerIndex => $value) {
                        $idArr = explode(",", $value);
                        foreach ($idArr as $idContent) {
                            $strSQL .= "insert into wf_layer_data(wf_uid,layer,data_uid,seq) ";
                            $strSQL .= "values('".$wfID."','".$layerIndex."','".$idContent."',0); ";
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
}
