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

    // 取得職務資料
    public function GetData_ApdDataAction(){
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

            $strSQL = "select * from apd_option t1 ";
            $strSQL .= "left join wf_option t2 on t1.wf_uid = t2.uid ";
            $strSQL .= "left join wf_title t3 on t2.title_id = t3.uid ";
            $strSQL .= "where 1 ";

            if(!empty($_GET["iUid"])){
                $strSQL .= "and t1.uid = '".$_GET["iUid"]."' "; 
            }
            $strSQL .= "and t1.ofid = '".$_GET["iOfid"]."' "; 
            // if(!empty($_GET["sys_code"])){
            $strSQL .= "and t1.sys_code_id = '".$_GET["sys_code"]."' "; 
            // }

            $strSQL .= "order by t1.uid asc ";
            $data = $SysClass->QueryData($strSQL);

            if(!empty($data)){
                $action["Data"] = $data;
                $action["Status"] = true;
            }else{
                $action["msg"] = '沒有資料';
                // $action["SQL"] = $strSQL;
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
                $psid = $_POST["psid"];
                $ofid = $_POST["ofid"];
                $faid = $_POST["faid"];
                $sys_code = $_POST["sys_code"];
                if($psid and $ofid and $faid and $sys_code){
                    
                    $strSQL = "insert into ass_position(psid, ofid, faid, sys_code_id) ";
                    $strSQL .= "values('".$psid."','".$ofid."',".$faid.",'".$sys_code."'); ";
                
                    if($SysClass->Execute($strSQL)){
                        $newID = $SysClass->NewInsertID();

                        $action["Data"] = $newID;
                        $action["Status"] = true;
                    }else{
                        $action["msg"] = '新增失敗';
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
