<?php
/**
 * form.class.php  form类
 *
 * @author           残梦  
 * @license          http://cms.canmeng.com
 * @lastmodify       2016-12-10 
 */

class form {
	
	/**
	 * input 
	 * @param $name name
	 * @param $value 默认值 如：cmCMS
	 * @param $required  是否为必填项 默认false
	 * @param $width  宽度 如：100
	 */
	public static function input($name = '', $value = '', $required=false, $width = 0) {
		$string = '<input class="input_text" ';
		if($width) $string .= ' style="width:'.$width.'px" ';
		if($required) $string .= ' required="required" ';
		$string .= ' name="'.$name.'" id="'.$name.'" ';
		$string .= ' type="text" value="'.$value.'">';
		return $string;
	}
		

	/**
	 * textarea
	 * @param $name name
	 * @param $value 默认值 如：cmCMS
	 * @param $required  是否为必填项 默认false
	 * @param $width  宽度 如：100
	 */
	public static function textarea($name = '', $value = '', $required=false, $width = 0) {
		$string = '<textarea name="'.$name.'" id="'.$name.'" ';
		if($width) $string .= ' width="'.$width.'px" ';
		if($required) $string .= ' required="required" ';
		$string .= '>'.$value.'</textarea>';
		return $string;
	}

	
	/**
	 * 下拉选择框
	 * @param $name name
	 * @param $val 默认选中值 如：1
	 * @param $array 一维数组 如：array('交易成功', '交易失败', '交易结果未知');
	 * @param $default_option 提示词 如：请选择交易 
	 */
	public static function select($name, $val = 0, $array = array(), $default_option = '') {
		$string = '<select name="'.$name.'" id="'.$name.'" class="select">';
		if($default_option) $string .= "<option value=''>$default_option</option>";
		if(!is_array($array) || count($array)== 0) return false;
		$ids = array();
		if(isset($val)) $ids = explode(',', $val);
		foreach($array as $value) {
			$selected = in_array($value, $ids) ? 'selected' : '';
			$string .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
		}
		$string .= '</select>';
		return $string;
	}
	
	
	/**
	 * 复选框
	 * 
	 * @param $name name
	 * @param $val 默认选中值，多个用 '逗号'分割 如：'1,2'
	 * @param $array 一维数组 如：array('交易成功', '交易失败', '交易结果未知');
	 */
	public static function checkbox($name, $val = '', $array = array()) {
		$string = '';
		$val = trim($val);
		if($val != '') $val = strpos($val, ',') ? explode(',', $val) : array($val);
		$i = 1;
		foreach($array as $value) {
			$value = trim($value);
			$checked = ($val && in_array($value, $val)) ? 'checked' : '';
			$string .= '<label class="option_label option_box" >';
			$string .= '<input type="checkbox" name="'.$name.'[]" id="'.$name.'_'.$i.'" '.$checked.' value="'.$value.'">'.$value;
			$string .= '</label>';
			$i++;
		}
		return $string;
	}

	
	/**
	 * 单选框
	 * 
	 * @param $name name
	 * @param $val 默认选中值 如：1
	 * @param $array 一维数组 如：array('交易成功', '交易失败', '交易结果未知');
	 */
	public static function radio($name, $val = '', $array = array()) {
		$string = '';
		foreach($array as $value) {
			$checked = trim($val)==trim($value) ? 'checked' : '';
			$string .= '<label class="option_label option_radio" >';
			$string .= '<input type="radio" name="'.$name.'" id="'.$name.'_'.$value.'" '.$checked.' value="'.$value.'">'.$value;
			$string .= '</label>';
		}
		return $string;
	}
	
	
	/**
	 * 验证码
	 * @param string $id   验证码ID
	 */
	public static function code($id='code') {
		return '<img src="'.U('api/index/code/').'" id="'.$id.'" onclick="this.src=this.src+\'?\'" style="cursor:pointer;" title="看不清？点击更换">';
	}
	
	
	/**
	 * 日期时间控件
	 * 
	 * @param $name name
	 * @param $val 默认值
	 * @param $isdatetime 是否显示时分秒
	 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
	 * @param $str 其他字符串，可加样式等
	 */
	public static function datetime($name, $val = '', $isdatetime = 0, $loadjs = 0, $str = '') {		
		$string = '';
		if($loadjs || !defined('DATETIME')) {
			define('DATETIME', 1);
			$string .= '<script type="text/javascript" src="'.STATIC_URL.'plugin/laydate/1.1/laydate.js"></script>';
		}
		
		$string .= '<input class="laydate-icon date"  value="'.$val.'" name="'.$name.'" id="'.$name.'" '.$str.'>';	
		$string .= '<script type="text/javascript"> laydate({elem: "#'.$name.'",';
		if($isdatetime) $string .= 'istime: true,format: "YYYY-MM-DD hh:mm:ss",';
		$string .= '});</script>';
		return $string;
	}
	
	
	/**
	 * 图片上传
	 * 
	 * @param $name name
	 * @param $val 默认值
	 * @param $style 样式
	 */
	public static function image($name, $val = '', $style = 'width:370px', $iscropper = false) {		
		$string = '<input class="input-text uploadfile" type="text" name="'.$name.'"  value="'.$val.'"  onmouseover="cm_img_preview(\''.$name.'\', this.value)" onmouseout="layer.closeAll();" id="'.$name.'" style="'.$style.'" > <a href="javascript:;" onclick="cm_upload_att(\''.U('attachment/api/upload_box', array('module'=>ROUTE_M, 'pid'=>$name)).'\')" class="btn btn-primary radius upload-btn"><i class="Hui-iconfont">&#xe642;</i> 浏览文件</a>';
		
		if($iscropper) $string = $string .' '.form::cropper($name);
		return $string;
	}
	
	
	/**
	 * 多图上传
	 * 
	 * @param $name name
	 * @param $val 默认值
	 * @param $n 上传数量
	 */
	public static function images($name, $val = '', $n = 20) {
		$string = '';
		$string .= '<fieldset class="fieldset_list"><legend>图片列表</legend><div class="fieldset_tip">您最多可以同时上传 <span style="color:red">'.$n.'</span> 个文件</div>
					<ul id="'.$name.'" class="file_ul">';
		if($val){
			$arr = string2array($val);
			foreach($arr as $key => $val){
				$string .= '<li>文件：<input type="text" name="'.$name.'[url][]" value="'.$val['url'].'" id="'.$name.'_'.$key.'" onmouseover="cm_img_preview(\''.$name.'_'.$key.'\', this.value)" onmouseout="layer.closeAll();" class="input-text w_300"> 描述：<input type="text" name="'.$name.'[alt][]" value="'.$val['alt'].'" class="input-text w_200"><a href="javascript:;" onclick="remove_li(this);">删除</a></li>';
			}
		}					
		$string .= 	'</ul></fieldset>
				<a href="javascript:;" onclick="cm_upload_att(\''.U('attachment/api/upload_box', array('module'=>ROUTE_M, 'pid'=>$name, 'n'=>$n)).'\')" class="btn btn-primary radius upload-btn mt-5"><i class="Hui-iconfont">&#xe642;</i> 浏览文件</a>';
		
		return $string;
	}
	

	/**
	 * 图像裁剪
	 * 
	 * @param $cid 		原图所在input的id
	 * @param $spec  	裁剪规则，1：3*2, 2:4*3, 3:1*1	
	 */
	public static function cropper($cid, $spec=1) {		
		$string = '<a href="javascript:;" onclick="cm_img_cropper(\''.$cid.'\', \''.U('attachment/api/img_cropper', array('spec'=>$spec)).'\')" class="btn btn-secondary radius upload-btn"><i class="Hui-iconfont">&#xe6bc;</i> 裁剪图片</a>';
		
		return $string;
	}
	
	
	/**
	 * 附件上传
	 * 
	 * @param $name name
	 * @param $val 默认值
	 * @param $style 样式
	 */
	public static function attachment($name, $val = '', $style='width:370px') {		
		$string = '<input class="input-text uploadfile" type="text" name="'.$name.'"  value="'.$val.'"  id="'.$name.'" style="'.$style.'" > <a href="javascript:;" onclick="cm_upload_att(\''.U('attachment/api/upload_box', array('module'=>ROUTE_M, 'pid'=>$name, 't'=>2)).'\')" class="btn btn-primary radius upload-btn"><i class="Hui-iconfont">&#xe642;</i> 浏览文件</a>';
		
		return $string;
	}	
	
	/**
	 * 编辑器
	 * 
	 * @param $name name
	 * @param $val 默认值
	 * @param $style 样式
	 * @param $isload 是否加载js,当该页面加载过编辑器js后，无需重复加载
	 */
	public static function editor($name = 'content', $val = '', $style='', $isload=false) {
		$style = $style ? $style : 'width:100%;height:400px';
		$string = '';
		if($isload) {
			$string .= '<script type="text/javascript" charset="utf-8" src="'.STATIC_URL.'plugin/ueditor/1.4.3.3/ueditor.config.js"></script>
			<script type="text/javascript" charset="utf-8" src="'.STATIC_URL.'plugin/ueditor/1.4.3.3/ueditor.all.min.js"> </script>
			<script type="text/javascript" charset="utf-8" src="'.STATIC_URL.'plugin/ueditor/1.4.3.3/lang/zh-cn/zh-cn.js"></script>';
		}
		$string .= '<script id="'.$name.'" type="text/plain" style="'.$style.'" name="'.$name.'">'.$val.'</script>
			<script type="text/javascript"> var ue = UE.getEditor(\''.$name.'\'); </script>';
		
		return $string;
	}
	
	
	/**
	 * 编辑器-Mini版
	 * 
	 * @param $name name
	 * @param $val 默认值
	 * @param $style 样式
	 * @param $isload 是否加载js,当该页面加载过编辑器js后，无需重复加载
	 */
	public static function editor_mini($name = 'content', $val = '', $style='', $isload=false) {
		$style = $style ? $style : 'width:100%;height:400px';
		$string = '';
		if($isload) {
			$string .= '<script type="text/javascript" charset="utf-8" src="'.STATIC_URL.'plugin/ueditor/1.4.3.3/ueditor.config.js"></script>
			<script type="text/javascript" charset="utf-8" src="'.STATIC_URL.'plugin/ueditor/1.4.3.3/ueditor.all.min.js"> </script>
			<script type="text/javascript" charset="utf-8" src="'.STATIC_URL.'plugin/ueditor/1.4.3.3/lang/zh-cn/zh-cn.js"></script>';
		}		
		$string .= '<script id="'.$name.'" type="text/plain" style="'.$style.'" name="'.$name.'">'.$val.'</script>
			<script type="text/javascript"> var ue = UE.getEditor("'.$name.'",{
            toolbars:[[ "fullscreen","source","|","undo","redo","|",
            "bold","italic","underline","blockquote","forecolor","|","fontfamily","fontsize","|","simpleupload","link","unlink","emotion","date","time","drafts"]],
            //关闭字数统计
            wordCount:false,
            //关闭elementPath
            elementPathEnabled:false,
            //默认的编辑区域高度
            initialFrameHeight:300
        }); </script>';
		
		return $string;
	}
	
}
