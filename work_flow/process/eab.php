<?php

class eab
{
    public function main($SysClass, $wfData)
    {
        // 預設不連資料庫
        // $SysClass->initialization();
        // 連線指定資料庫
        // $SysClass->initialization("設定檔[名稱]",true); -> 即可連資料庫
        // 連線預設資料庫
        // $SysClass->initialization(null,true);
        try{
            $strSQL = "select t2.*,t3.data_uid,t3.layer,t1.sys_code_id from apd_option t1 ";
            $strSQL .= "left join apd_data t2 on t1.uid = t2.apd_uid ";
            $strSQL .= "left join wf_layer_data t3 on t2.wf_layer_id = t3.uid ";
            $strSQL .= "where t1.wf_uid = ".$wfData["uid"]." and t2.status = 0 ";
            $data = $SysClass -> QueryData($strSQL);
            // 如果不是空的，將執行找到該部門最高的人
            if(!empty($data)){
                // 這邊用到的data_uid = ass_org uid
                // 先整理
                $layerData = array();
                $layer = array();
                foreach ($data as $content) {
                    $layerData[$content["layer"]][] = $content;
                    if(!in_array($content["layer"],$layer)){
                        array_push($layer, $content["layer"]);
                    }
                }
                // print_r($layer);

                // 從最低層級的開始找起
                $minLayer = min($layer);
                // print_r($layerData[$minLayer]);
                foreach ($layerData[$minLayer] as $content) {
                    // 開始找聯絡方式
                    $strSQL = "select t3.remark as mail from ass_user t1 ";
                    $strSQL .= "left join ass_position t2 on t1.posid = t2.uid ";
                    $strSQL .= "left join ass_common_communication t3 on t1.cmid = t3.cmid ";
                    $strSQL .= "where t1.sys_code_id = '".$content["sys_code_id"]."' ";
                    $strSQL .= "and t1.orgid = '".$content["data_uid"]."' ";
                    $strSQL .= "and t2.faid = 0 and t3.type = 2";
                    $mail = $SysClass->QueryData($strSQL);
                    if(!empty($mail)){
                        print_r($mail);
                    }
                    // echo $strSQL."\n";
                }

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
