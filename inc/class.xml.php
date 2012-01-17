<?php
class just_xml{
	var $VERSION='0.01b';

	var $CASE_FOLDING=0;                                               # переводить названия тэгов в заглавные? (не рекомендуется)
	var $SKIP_WHITE=1;                                                 # пропускать пробелы, переносы строк? (рекомендуется)
	//var $ACCEPT_CHARSET='k';    # не поддерживается в этой версии      # кодировка, в которую будут "на лету" перегоняться данные XML при чтении
	//var $USE_32bits_sums=1;     # не поддерживается в этой версии      # добавлять 32-х битный хэш размера файла XML в конец этого файла?
	var $headline = '';    			# первая строка XML-файла
	var $xml=false;                                                    # контейнер, содержащий собранный (или прочтённый, загруженный) XML (как `string`)
	var $values=false;                                                 # . контейнер, используемый разборщиком XML
	var $index=false;                                                  # . контейнер, используемый разборщиком XML
	var $tree=false;                                                   # контейнер, содержащий разобранный XML (как `array`)

	/**************************************************************************
		(constructor) xml();
	**************************************************************************/
	function just_xml(){
		$this->headline ='<?xml version="1.0" encoding="utf-8" ?'.'>';
		//return $this;
	}

	/**************************************************************************
		(method) xml2arr($fileName=='*file.xml'&&$strXml=='<..></..>');
	**************************************************************************/
	function xml2arr($xml=false){
		if(!$xml) return false;
		if(substr(trim($xml), 0, 1)=='*'&&is_readable(substr(trim($xml), 1))) $this->xml=str_replace("\r", '', implode('', file(substr(trim($xml), 1))));
		elseif(substr(trim($xml), 0, 1)=='*'&&!is_readable(substr(trim($xml), 1))) die('(class)xml: Can\'t read file.');
		else $this->xml=$xml;

		$pr=xml_parser_create();
		xml_parser_set_option($pr, XML_OPTION_CASE_FOLDING, $this->CASE_FOLDING);
		xml_parser_set_option($pr, XML_OPTION_SKIP_WHITE, $this->SKIP_WHITE);
		xml_parse_into_struct($pr, $this->xml, $this->values, $this->index);
		xml_parser_free($pr);

		$i=-1;
		$this->tree=$this->_get_depth($this->values, $i);
		return $this->tree;
	}

	/**************************************************************************
		(method) arr2xml($arr[array], $headline[str]=false, $fileName[str]=false);
	**************************************************************************/
	function arr2xml($arr=false, $headline=false, $fileName=false){
		if(!$arr) return false;
		$xml=$headline?$headline:$this->headline; $xml.="\n";
		$xml.=$this->_get_xml($arr, 0);
		$this->xml=$xml;

		if($fileName){
			if(!($fh=fopen($fileName, 'w'))) die('(class)xml: Can\'t write file!');
			flock($fh, LOCK_EX);
			$byte=fputs($fh, $xml);
			fclose($fh);
		}
		return $fileName?$byte:$xml;
	}

	/**************************************************************************
		(system method) _get_depth($vals[array], &$i[int]);
	**************************************************************************/
	function _get_depth($vals, &$i){
		$child=array();
		if(isset($vals[$i]['value'])) $child[]=$vals[$i]['value'];
		while(++$i<count($vals)){
			$type=$vals[$i]['type'];
			if($type=='open'){
				if(isset($vals[$i]['attributes'])) $attr=$vals[$i]['attributes']; else $attr=array();
				$child[$vals[$i]['tag']][]=array(
					'#'=>$this->_get_depth($vals, $i),
					'@'=>$this->_decode_attributes($attr),
					);
			}elseif($type=='cdata'){
				$child[]=$this->_unconv_cdata($vals[$i]['value']);
			}elseif($type=='complete'){
				if(isset($vals[$i]['attributes'])) $attr=$vals[$i]['attributes']; else $attr=array();
				if(isset($vals[$i]['value'])) $value=$vals[$i]['value']; else $value=null;
				$child[$vals[$i]['tag']][]=array(
					'#'=>$value,
					'@'=>$this->_decode_attributes($attr),
					);
			}elseif($type=='close'){
				return $child;
			}
		}
		return $child;
	}

	/**************************************************************************
		(system method) _get_xml($arr[array], $tabsi[int]);
	**************************************************************************/
	function _get_xml($arr, $tabsi){
		$source=null; $tabs=null;
		$i=0; while(++$i<=$tabsi) $tabs.="\t";

		foreach($arr as $k0=>$v0) foreach($v0 as $k1=>$v1){
			if(is_array($v1['#'])){
				$tabsi++;
				$source.="$tabs<$k0".$this->_encode_attributes($v1['@']).">\n".$this->_get_xml($v1['#'], $tabsi)."$tabs</$k0>\n";
				$tabsi--;
			}elseif($v1['#']===null){
				$source.="$tabs<$k0".$this->_encode_attributes($v1['@'])." />\n";
			}else{
				$source.="$tabs<$k0".$this->_encode_attributes($v1['@']).">".$this->_cdata($v1['#'])."</$k0>\n";
			}
		}

		if($tabsi==0) $source=substr($source, 0, strlen($source)-1);
		return $source;
	}

	/**************************************************************************
		(system other methods)
	**************************************************************************/
	function _cdata($s){
		if(preg_match("/['\"\[\]<>&]/", $s)) $s='<![CDATA['.$this->_conv_cdata($s).']]>';
		$s=str_replace("\r", '', $s);
		return $s;
	} # //
	function _conv_cdata($s){
		$s=str_replace('<![CDATA[', '<!ў|CDATA|', $s);
		$s=str_replace(']]>', '|ў]>', $s);
		return $s;
	}
	function _unconv_cdata($s){
		$s=str_replace('<!ў|CDATA|', '<![CDATA[', $s);
		$s=str_replace('|ў]>', ']]>', $s);
		return $s;
	} # //
	function _encode_attributes($arr){
		$s='';
		foreach($arr as $k=>$v) $s.=" $k=\"".$this->_encode_attribute($v)."\"";
		return $s;
	} # //
	function _decode_attributes($arr){
		foreach($arr as $k=>$v) $arr[$k]=$this->_decode_attribute($v);
		return $arr;
	} # //
	function _encode_attribute($s){
		$s=preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $s);
		$s=str_replace("<", "&lt;" , $s);
		$s=str_replace(">", "&gt;", $s);
		$s=str_replace('"', "&quot;", $s);
		$s=str_replace("'", '&#039;', $s);
		return $s;
	}
	function _decode_attribute($s){
		$s=str_replace("&amp;", "&", $s);
		$s=str_replace("&lt;" , "<", $s);
		$s=str_replace("&gt;" , ">", $s);
		$s=str_replace("&quot;", '"', $s);
		$s=str_replace("&#039;", "'", $s);
		return $s;
	}
}

?>
