<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class customersController
{

    //客戶資料註冊
    public function registeredAction()
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
                $name = $_POST["name"];
                $phone = $_POST["phone"];
                $address = $_POST["address"];
                $org_numbers = $_POST["org_numbers"];
                $principal_name = $_POST["principal_name"];
                $principal_phone = $_POST["principal_phone"];
                $principal_mail = $_POST["principal_mail"];
                if($name and $phone and $address){
                    $strSQL = "insert into cl_customers(name,phone,address,org_numbers,principal_name,principal_phone,principal_mail) ";
                    $strSQL .= "values('".$name."','".$phone."','".$address."','".$org_numbers."','".$principal_name."','".$principal_phone."','".$principal_mail."');";
                    if($SysClass->Execute($strSQL)){
                        $newID = $SysClass->NewInsertID();
                        $customersID = $newID;

                        $code = base64_encode(time());
                        $code = str_replace("==", "", $code);

                        $strSQL = "insert into sys_code(code,customers_uid)";
                        $strSQL .= "values('".$code."',".$newID.")";
                        if($SysClass->Execute($strSQL)){
                            $newID = $SysClass->NewInsertID();
                            // 向帳號中心註冊管理員帳號
                            $APIUrl = $SysClass->GetAPIUrl('rsApiURL');
                            $APIUrl .= "adminRegisteredAPI/registered";

                            $user_ac = "admin".substr($code, strlen($code)-5,strlen($code));

                            $sendData = array();
                            $sendData["sys_code_uid"] = $newID;
                            $sendData["user_ac"] = $user_ac;
                            //密碼與帳號相同
                            $sendData["user_pw"] = $user_ac;
                            $sendData["customersID"] = $customersID;

                            // 送出
                            $adnminReg = $SysClass->UrlDataPost($APIUrl,$sendData);
                            $adnminReg = $SysClass->Json2Data($adnminReg["result"],false);
                            if($adnminReg["status"]){
                                $action["data"] = $adnminReg["data"];
                                $action["status"] = true;
                            }else{
                                $action["msg"] = '管理員帳號註冊失敗，請重新嘗試';
                            }
                        }else{
                            $action["msg"] = '系統代碼新增發生錯誤，請重新嘗試';
                        }
                    }else{
                        $action["msg"] = '新增發生錯誤，請重新嘗試';
                    }
                }else{
                    $action["msg"] = '必填欄位未有資料';
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

    //客戶資料列表
    public function cuslistAction()
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

            $strSQL = "select t1.*, t2.uid as codeID from cl_customers t1 ";
            $strSQL .= "left join sys_code t2 on t1.uid = t2.customers_uid ";
            $data = $SysClass->QueryData($strSQL);
            if(!empty($data)){
                // 與帳號中心取得管理員帳號資料

                $APIUrl = $SysClass->GetAPIUrl('rsApiURL');
                $APIUrl .= "adminRegisteredAPI/adminlist";

                $sendData = array();
                // 送出
                $adnminList = $SysClass->UrlDataPost($APIUrl,$sendData);
                $adnminList = $SysClass->Json2Data($adnminList["result"],false);

                foreach ($data as $dataKey => $dataContent) {
                
                    foreach ($adnminList["data"] as $aListKey => $content) {
                        if($content["sys_code_uid"] == $dataContent["codeID"]){
                            $data[$dataKey]["admin"] = $content["user_ac"];
                            unset($adnminList["data"][$aListKey]);
                        }
                    }
                }
                $action["data"] = $data;
                $action["status"] = true;
            }else{
                $action["msg"] = "沒有資料";
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
