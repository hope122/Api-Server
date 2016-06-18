<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class AssCommonAddressController
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

    // 取得自然人地址資料
    public function GetData_AssCommonAddressAction(){
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
            if(!empty($_GET["iCmid"])){
                $strSQL = "select * from ass_common_address ";
                $strSQL .= "where 1 ";
                $strSQL .= "and cmid = '".$_GET["iCmid"]."' "; 
                if($_GET["iAddr_type"] == 1){
                    $strSQL .= "and addr_type = '1' "; 
                }else if($_GET["iAddr_type"] == 0){
                    $strSQL .= "and addr_type = '0' "; 
                }
                $strSQL .= "order by uid asc ";
                $data = $SysClass->QueryData($strSQL);

                if(!empty($data)){
                    $action["Data"] = $data;
                    $action["Status"] = true;
                }else{
                    $action["msg"] = '沒有資料';
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

    // 新增自然人地址資料
    public function Insert_AssCommonAddressAction(){
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
                $cmid = $_POST["cmid"];
                $addr_type = $_POST["addr_type"];
                $zip = $_POST["zip"];
                $city = $_POST["city"];
                $area = $_POST["area"];
                $vil = $_POST["vil"];
                $verge = $_POST["verge"];
                $road = $_POST["road"];
                $addr = $_POST["addr"];
                if($cmid and $addr_type >= 0){
                    $strSQL = "insert into ass_common_address(cmid,addr_type,zip,city,area,vil,verge,road,addr) ";
                    $strSQL .= "values('".$cmid."','".$addr_type."','".$zip."','".$city."','".$area."','".$vil."','".$verge."','".$road."','".$addr."'); ";

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

    // 修改自然人地址資料
    public function Update_AssCommonAddressAction(){
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
                $uid = $_POST["uid"];
                $zip = $_POST["zip"];
                $city = $_POST["city"];
                $area = $_POST["area"];
                $vil = $_POST["vil"];
                $verge = $_POST["verge"];
                $road = $_POST["road"];
                $addr = $_POST["addr"];
                if($uid){
                    $strSQL = "update ass_common_address set zip='".$zip."',city='".$city."',area='".$area."',vil='".$vil."',verge='".$verge."',road='".$road."',addr='".$addr."' ";
                    $strSQL .= "where uid='".$uid."'; ";

                    if($SysClass->Execute($strSQL)){
                        $action["Data"] = '修改成功';
                        $action["Status"] = true;

                    }else{
                        $action["msg"] = '修改失敗';
                        $action["sql"] = $strSQL;
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

    //刪除自然人地址資料
    public function Delete_AssCommonAddressAction()
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
                $strSQL = "delete from ass_common_address where uid = '".$_DELETE["iUid"]."'";
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
