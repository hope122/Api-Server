<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class AssOrgController
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

    // 取得組織資料
    public function GetData_AssOrgAction(){
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

            $strSQL = "select t1.*,t2.name from ass_org t1 ";
            $strSQL .= "left join ass_type_office t2 on t1.officeid = t2.uid ";
            $strSQL .= "where 1 ";

            if(!empty($_GET["iUid"])){
                $strSQL .= "and t1.uid = '".$_GET["iUid"]."' "; 
            }

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
    // 以下ORG暫時用不到
    // ---------------------------------------------------------------------
    // 新增自然人資料
    public function Insert_AssUserAction(){
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
                $uuid = $_POST["uuid"];
                $cmid = $_POST["cmid"];
                $sid = $_POST["sid"];
                $orgid = $_POST["orgid"];
                $posid = ($_POST["posid"]) ? $_POST["posid"]:"NULL";
                $sys_code_id = $_POST["sys_code"];
                $datec = date("Y/m/d");
                if($uuid and $cmid and $orgid and $sys_code_id){
                    // 先檢查有沒有新增過，一個系統僅允許一筆使用者資料
                    $strSQL = "select * from ass_user where cmid = '".$cmid."' and sys_code_id = '".$sys_code_id."'";
                    $data = $SysClass->QueryData($strSQL);
                    $user_ac = $sid;
                    // 沒有新增，進行新增
                    if(empty($data)){
                        $strSQL = "insert into ass_user(cmid, orgid, posid, datec, sys_code_id) ";
                        $strSQL .= "values('".$cmid."','".$orgid."',".$posid.",'".$datec."','".$sys_code_id."'); ";
                    
                        if($SysClass->Execute($strSQL)){

                            $newID = $SysClass->NewInsertID();
                        // 向帳號中心註冊管理員帳號
                            $userReg = $this->regUser($SysClass, $sys_code_id, $user_ac, $uuid, $newID);

                            if($userReg["status"]){
                                $action["Data"] = $newID;
                                $action["Status"] = true;
                            }else{
                                $action["msg"] = '帳號註冊失敗，請重新嘗試';
                                $action["accenter_msg"] = $userReg["msg"];
                            }

                        }else{
                            $action["msg"] = '新增失敗';
                        }
                    }else{
                        // 如果該筆自然人資料已經新增過，則進行帳號中心註冊
                        $userReg = $this->regUser($SysClass, $sys_code_id, $user_ac, $uuid, $data[0]["uid"]);

                        if($userReg["status"]){
                            $action["Data"] = $data[0]["uid"];
                            $action["msg"] = $userReg["msg"];
                            $action["Status"] = true;
                        }else{
                            $action["msg"] = '帳號註冊失敗，請重新嘗試';
                            $action["accenter_msg"] = $userReg["msg"];

                        }

                    }
                }else{
                    $action["msg"] = '參數不可為空'.$sys_code_id;

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

    // 註冊一般使用者
    private function regUser($SysClass, $sys_code_id, $user_ac, $uuid, $userID){
        // 向帳號中心註冊一般使用者帳號
        $APIUrl = $SysClass->GetAPIUrl('rsApiURL');
        $APIUrl .= "userRegisteredAPI/registered";

        $sendData = array();
        $sendData["sys_code_uid"] = $sys_code_id;
        $sendData["user_ac"] = $user_ac;
        //密碼與帳號相同
        $sendData["user_pw"] = $user_ac;
        $sendData["create_uuid"] = $uuid;
        $sendData["bps_user_uid"] = $userID;
        // print_r($sendData);
        // 送出
        $userReg = $SysClass->UrlDataPost($APIUrl,$sendData);
        $userReg = $SysClass->Json2Data($userReg["result"],false);
        return $userReg;
    }

    // 修改自然人資料
    public function Update_AssUserAction(){
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
    public function Delete_AssUserAction()
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
