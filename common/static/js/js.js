/**
 * cmCMS内容管理系统
 * @author           残梦  
 * @license          http://cms.canmeng.com
 */

$(function(){ 

	$("#searchselected").click(function(){ 
		$("#searchtab").toggle();
		if($(this).hasClass('searchopen')){
			$(this).removeClass("searchopen");
		}else{
			$(this).addClass("searchopen");
		}
	}); 

	$("#searchtab li").hover(function(){
		$(this).addClass("selected");
	},function(){
		$(this).removeClass("selected");
	});
	 
	$("#searchtab li").click(function(){
		$("#modelid").val($(this).attr('data') );
		$("#searchselected").html($(this).html());
		$("#searchtab").hide();
		$("#searchselected").removeClass("searchopen");
	});
	
});

// 做最好用的开源CMS: cmCMS ，官方QQ交流群：161208398

function toreply(obj){
    if($("#rep_" + obj).css("display") == "none"){
        $("#rep_" + obj).css("display", "block");
    }else{
        $("#rep_" + obj).css("display", "none");
    }
}

function check_comm(obj){
    var content = obj.content.value;
	if(content === ''){
	    alert('你不打算说点什么吗？');
		return false;
	}
	return true;
}

function check_rep(obj){
    var content = obj.content.value;
	if(content === ''){
	    alert('你不打算说点什么吗？');
		return false;
	}
	return true;
}