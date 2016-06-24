<?php		
	namespace ctrlSocketSettingService;
	
	
	class ctrlSocketSetting {
		#取得API設定檔
		public function GetSocketUrl($iniIndex, $original){
			$SysClass = new \ctrlToolsService\ctrlTools;
			$strIniFile = dirname(__DIR__) . "\\..\\..\\..\\..\\config\\socket.ini";
            //開啟ＡＰＩ設定檔
            $APIConfing = $SysClass->GetINIInfo($strIniFile,null,"socket",'',true);
            if(!$original){
            	if($iniIndex){
            		return $APIConfing[$iniIndex];
				}else{
		            return $APIConfing;

				}
            }else{
	            return $APIConfing;
            }
		}
	}
?>