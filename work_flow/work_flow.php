<?php
// 載入設定檔
$SystemCtrlPath = dirname(__DIR__) . '\\public\\include\\SystemCtrl\\SystemCtrl.php';
if(!file_exists($System_APServicePath)){
    $SystemCtrlPath = dirname(__DIR__) . '/public/include/SystemCtrl/SystemCtrl.php';
}

include($SystemCtrlPath);

use SystemCtrl\ctrlSystem;
// 載入process內的PHP檔案
$processPath = glob( __DIR__ ."\\process\\*.php");
if(!empty($processPath)){
    foreach($processPath as $systemApContent){
        include_once($systemApContent);
    }
}else{
    //先載入各物件
    $processPath = glob( __DIR__ ."/process/*.php");
    if(!empty($processPath)){
        foreach($processPath as $systemApContent){
            include_once($systemApContent);
        }
    }
}


class workflow
{

    //wf 主要的函式
    public function main()
    {
        $SysClass = new ctrlSystem;
        // 預設不連資料庫
        // $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        $SysClass->initialization(null,true);
        try{
            
            // print_r($iniFile);
            // for

            $strSQL = "select * from wf_option";
            $data = $SysClass->QueryData($strSQL);
            if(!empty($data)){
                // print_r($data);
                foreach ($data as $content) {
                    $menuSystem = new $content["menu_code"]();
                    $menuSystem-> main($SysClass, $content);
                }
            }else{
                echo "執行結束";
            }
        }catch(Exception $error){
            //依據Controller, Action補上對應位置, $error->getMessage()為固定部份
            $SysClass->WriteLog("work flow", "main", $error->getMessage());
        }
        //關閉資料庫連線
        // $SysClass->DBClose();
        //釋放
        $SysClass = null;
    }

    
}
$workflow = new workflow;
$workflow->main();
