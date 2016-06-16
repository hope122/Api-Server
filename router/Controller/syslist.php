<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class syslistController
{

    //客戶資料列表
    public function indexAction()
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
            if(!empty($_POST["sysList"])){
                $sysList = $_POST["sysList"];
                $sysStr = "";
                foreach ($sysList as $value) {
                    $sysStr .= $value .",";
                }
                $sysStr = substr($sysStr, 0, strlen($sysStr)-1);
                
                $strSQL = "select t1.name, t2.uid as codeID from cl_customers t1 ";
                $strSQL .= "left join sys_code t2 on t1.uid = t2.customers_uid ";
                $strSQL .= "where t2.uid in (".$sysStr.")";
                $data = $SysClass->QueryData($strSQL);
                if(!empty($data)){
                    $action["data"] = $data;
                    $action["status"] = true;
                }else{
                    $action["msg"] = "沒有資料";
                }
            }else{
                $action["msg"] = "sysList參數不可為空";
            }
           
            
            $pageContent = $SysClass->Data2Json($action);
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("SupplyController", "editorAction", $error->getMessage());
        }
        //關閉資料庫連線
        $SysClass->DBClose();
        //釋放
        $SysClass = null;
        $this->viewContnet['pageContent'] = $pageContent;
    }

}
