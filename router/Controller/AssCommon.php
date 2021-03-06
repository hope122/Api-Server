<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class AssCommonController
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

    // 取得自然人資料
    public function GetData_AssCommonAction(){
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

            $strSQL = "select t2.*,t1.uid as userID, t3.remark as mail , t4.remark as phone from ass_user t1 ";
            $strSQL .= "left join ass_common t2 on t1.cmid = t2.uid ";
            $strSQL .= "left join ass_common_communication t3 on (t2.uid = t3.cmid and t3.type = 2) ";
            $strSQL .= "left join ass_common_communication t4 on (t2.uid = t4.cmid and t4.type = 2) ";
            // $strSQL .= "left join ass_common_address t2 on t1.uid = t2.cmid ";
            $strSQL .= "where 1 ";

            if(!empty($_GET["iUid"])){
                $strSQL .= "and t2.uid = '".$_GET["iUid"]."' "; 
            }
            if(!empty($_GET["sSid"])){
                $strSQL .= "and t2.sid = '".$_GET["sSid"]."' "; 
            }
            // if(!empty($_GET["sys_code"])){
            $strSQL .= "and t1.sys_code_id = '".$_GET["sys_code"]."' "; 
            // }

            $strSQL .= "order by t2.uid asc ";
            $data = $SysClass->QueryData($strSQL);

            if(!empty($data)){
                // 找電話&mail

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

    // 新增自然人資料
    public function Insert_AssCommonAction(){
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
                $name = $_POST["name"];
                $sid = $_POST["sid"];
                $birthday = $_POST["birthday"];
                $sex = $_POST["sex"];
                $sys_code_id = $_POST["sys_code"];
                // mail
                $mail = $_POST["mail"];
                if($name and $sid and $birthday and $sex and $sys_code_id){
                    $strSQL = "insert into ass_common(name,sid,birthday,sex) ";
                    $strSQL .= "values('".$name."','".$sid."','".$birthday."','".$sex."'); ";

                    

                    if($SysClass->Execute($strSQL)){

                        $newID = $SysClass->NewInsertID();

                        // 新增mail
                        $strSQL = "insert into  ass_common_communication(cmid,type,remark) ";
                        $strSQL .= "values('".$newID."',2,'".$mail."'); ";
                        $SysClass->Execute($strSQL);

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
    public function Update_AssCommonAction(){
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
                $name = $_POST["name"];
                $sid = $_POST["sid"];
                $birthday = $_POST["birthday"];
                $sex = $_POST["sex"];
                $uid = $_POST["uid"];
                // 修改mail
                $mail = $_POST["mail"];
                if($name and $sid and $birthday and $sex and $uid){
                    $strSQL = "select * from ass_common_communication ";
                    $strSQL .= "where cmid = '".$uid."' ";
                    $data = $SysClass->QueryData($strSQL);
                    if(!empty($data)){
                        // 修正mail
                        $strSQL = "update ass_common_communication set remark='".$mail."' ";
                        $strSQL .= "where cmid='".$uid."'; ";

                    }else{
                        // 新增
                        $strSQL = "insert into  ass_common_communication(cmid,type,remark) ";
                        $strSQL .= "values('".$uid."',2,'".$mail."'); ";
                    }
                    $strSQL .= "update ass_common set name='".$name."',sid='".$sid."',birthday='".$birthday."',sex='".$sex."' ";
                    $strSQL .= "where uid='".$uid."'; ";

                    if($SysClass->Execute($strSQL)){
                        $action["Data"] = '修改成功';
                        $action["Status"] = true;

                    }else{
                        $action["msg"] = '修改失敗';
                        // $action["msg"] = $strSQL;
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
    public function Delete_AssCommonAction()
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
                $strSQL = "delete from ass_common where uid = '".$_DELETE["iUid"]."'";
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
