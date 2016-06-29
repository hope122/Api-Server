<?php
/*
    Example Method for api
*/
use SystemCtrl\ctrlSystem;

class SysPushMsgController
{
    public $push_webMsg;
    public $push_mail;

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

    // 單純推播
    public function Push_WebSpecifiedMsgAction()
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

            if(!empty($_POST)){
                $sysCode = $_POST["sysCode"];
                $userID = $_POST["userID"];
                $msg = $_POST["msg"];
                if($sysCode and $userID and $msg){
                    // 取得設定檔
                    $socketSet = $SysClass->GetSocketUrl();
                    $socketUrl = $socketSet["server"];
                    $socketPort = $socketSet["port"];
                    // socketIO 連線
                    $SysClass->setSocket($socketUrl,$socketPort);

                    // 設定要傳送的訊息
                    $sendData = array();
                    $sendData['sysCode'] = $sysCode;
                    $sendData['userID'] = $userID;
                    $sendData['msg'] = $msg;

                    $SysClass->socketSend("sysPushSpecified",$sendData);
                    $action["status"] = true;
                    $action['msg'] = "已發送訊息";

                }else{
                    $action['msg'] = "sysCode、userID、msg參數不可為空";

                }
                

            }else{
                $action['msg'] = "不支援POST以外的方法";
            }
            $this->push_webMsg = $action;
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

    // 單純寄信
    public function Push_MailAction()
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

            if(!empty($_POST)){
                // recipient,$mailTitle,$msg
                $sender = $_POST["sender"];
                $recipient = $_POST["recipient"];
                $mailTitle = $_POST["mailTitle"];
                $msg = $_POST["msg"];
                if($sender and $recipient and $mailTitle and $msg){
                    
                    if($SysClass -> Tomail($sender,$recipient,$mailTitle,$msg)){
                        $action["status"] = true;
                        $action["msg"] = "發送成功";
                    }else{
                        $action["msg"] = "發送失敗";
                    }

                }else{
                    $action['msg'] = "sender、recipient、mailTitle、msg參數不可為空";

                }
                

            }else{
                $action['msg'] = "不支援POST以外的方法";
            }
            $this->push_mail = $action;
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

    public function Push_WebMsgAndMailAction(){
        $this->Push_WebSpecifiedMsgAction();
        $this->Push_MailAction();

        $action["status"] = true;
        $action["mail"] = $this->push_mail;
        $action["webMsg"] = $this->push_webMsg;

        $pageContent = json_encode($action);
        $this->viewContnet['pageContent'] = $pageContent;
    }
}
