一、获取列表信息标签:
{m:lists field="title,thumb,catid,color,url,updatetime" catid="1" limit="10"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称，例如: field="title,thumb,catid,color,url,updatetime" 全部字段请在后台-模型管理-字段管理中查看
catid ------获取栏目ID，例如: catid="1"
modelid ------获取模型ID，例如: modelid="1" 
thumb ------筛选条件，例如: thumb="1" 加该属性则表示只调用有缩略图的信息
flag  ------内容属性，例如: flag="1"  属性说明: 置顶[1]  头条[2]  特荐[3] 推荐[4] 热点[5] 幻灯[6] 跳转[7]    
where ------条件属性，例如: where="nickname='admin'"  初学者不建议用该属性
order ------排序规则，例如: order="RAND()" 表示内容随机读取
limit ------限制条数，例如: limit="10" 默认值为20
page  ------栏目分页，例如：page="page" 如不需要分页，请不要用该属性


二、点击排行榜标签:

{m:hits field="title,thumb,catid,url" catid="1" limit="10" cache="3600"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称，例如: field="title,thumb,catid,url" 全部字段请在后台-模型管理-字段管理中查看
catid ------获取栏目ID，例如: catid="1"
modelid ------获取modelid，例如: modelid="1" 
thumb ------筛选条件，例如: thumb="1" 加该属性则表示只调用有缩略图的信息
flag  ------内容属性，例如: flag="1"  属性说明: 置顶[1]  头条[2]  特荐[3] 推荐[4] 热点[5] 幻灯[6] 跳转[7]    
where ------条件属性，例如: where="nickname='admin'"  初学者不建议用该属性
order ------排序规则，例如: order="id DESC" 可能的值有“id DESC”或“id ASC”，默认为“id DESC”
limit ------限制条数，例如: limit="10" 默认值为20
cache ------缓存时间，YzmCMS V5.8新增，详情查看：https://www.yzmcms.com/dongtai/106.html


三、友情链接标签:

{m:link field="url,logo,name" limit="20"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称，例如: field="url,logo,name" 
thumb ------筛选条件，例如: thumb="1" 加该属性则表示只调用有logo的信息
order ------排序规则，例如: order="listorder ASC"
typeid ------链接范围，0默认,1首页,2列表页,3内容页，例如 typeid="1"
where ------条件属性，例如: where="status=1"  初学者不建议用该属性
limit ------限制条数，例如: limit="10" 默认值为20


四、导航标签:

{m:nav field="catid,catname,arrchildid,pclink" where="parentid=0" limit="20"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称，例如: field="catid,catname,arrchildid,pclink"
order ------排序规则，例如: order="listorder ASC"
where ------条件属性，例如: where="parentid=1"  初学者不建议用该属性
limit ------限制条数，例如: limit="10" 默认值为20

五、TAG标签:

{m:tag field="id,tag,total" limit="20"}

可能存在的属性:

属性名------解释说明

catid ------此属性可选，若加此属性则表示只查询绑定本栏目的TAG
field ------获取字段名称，例如: field="id,tag,total"
order ------排序规则，例如: order="id ASC"
limit ------限制条数，例如: limit="10" 默认值为20

六、留言板标签:
{m:guestbook limit="5" page="page"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称
order ------排序规则，例如: order="id DESC"
limit ------限制条数，例如: limit="10" 默认值为20
page  ------栏目分页，page="page" 如不需要分页，请不要用该属性

七、评论列表标签:
{m:comment_list modelid="$modelid" catid="$catid" id="$id" limit="20"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称
id    ------获取内容ID，例如: id="1"
catid ------获取栏目ID，例如: catid="1"
modelid ------获取modelid，例如: modelid="1" 
order ------排序规则，例如: order="id DESC"
limit ------限制条数，例如: limit="10" 默认值为20


八、评论排行榜标签:

{m:comment_ranking  modelid="1" limit="10" cache="3600"}

可能存在的属性:

属性名------解释说明

field ------获取字段名称
modelid ------获取modelid，例如: modelid="1" 
limit ------限制条数，例如: limit="10" 默认值为20
cache ------缓存时间，YzmCMS V5.8新增，详情查看：https://www.yzmcms.com/dongtai/106.html

九、自定义SQL标签(万能的SQL标签):
{m:get sql=""}

可能存在的属性:

属性名------解释说明

sql ------书写您的SQL语句，例如：sql="SELECT * FROM yzmcms_admin" 表前缀yzmcms_表示通用前缀，无需修改，结果返回二维数组，初学者不建议用
limit ------限制条数，例如: limit="10" 默认值为20
page ------ 内容分页，例如：page="page" 如不需要分页，请不要用该属性
YzmCMS 3.4及以上版本新增banner标签（轮播图标签）：
{m:banner field="title,image,url,typeid,status" limit="10"}

属性名------解释说明
field ------获取字段名称，例如: field="title,image,url,typeid,status",此属性可选
typeid------轮播分类，例如：typeid="1",此属性可选,轮播分类ID可在后台轮播图分类管理中查看
limit ------限制条数，例如: limit="10" 默认值为20,此属性可选

范例：
<ul>
	{loop $data $v}	
	<li><a href="{$v[url]}"><img src="{$v[image]}" alt="{$v[title]}" title="{$v[title]}"></a></li>
	{/loop}
<ul>


YzmCMS 3.7及以上版本新增以下四个标签：
相关内容标签（有相同tag标签的内容视为相关内容）：

{m:relation field="title,url,thumb" modelid="$modelid" id="$id" limit="5"}

可能存在的属性:

属性名------解释说明
field ------获取字段名称，例如: field="title,thumb,catid,url" 全部字段请在后台-模型管理-字段管理中查看
modelid------模型id，必填
id------当前文章id，必填
limit ------限制条数，例如: limit="10" 默认值为20,此属性可选

范例：
{loop $data $v}
	<a href="{$v[url]}" target="_blank">{$v[title]}</a>
{/loop}


内容页tag标签（如图）：

yzmcms标签

{m:centent_tag modelid="$modelid" id="$id" limit="10"}

可能存在的属性:

属性名------解释说明
modelid------模型id，必填
id------当前文章id，必填
limit ------限制条数，例如: limit="10" 默认值为20,此属性可选

范例：
{loop $data $v}
	<a href="{U('search/index/tag',array('id'=>$v['id']))}" target="_blank">{$v[tag]}</a>
{/loop}
文章归档标签：

{m:content_archives modelid="1" type="2" limit="10" cache="3600"}

可能存在的属性:

属性名------解释说明
modelid------模型id，必填
type------显示类型分为1(例如：2018-01)，或者2(例如：2018年01月)
limit ------限制条数，例如: limit="10" 默认值为20,此属性可选
cache ------缓存时间，YzmCMS V5.8新增，详情查看：https://www.yzmcms.com/dongtai/106.html

范例：
<ul>
{loop $data $v}	
	<li><a href="{U('search/index/archives',array('pubtime'=>$v['inputtime']))}" target="_blank">{$v[pubtime]}({$v[total]})</a></li>
{/loop} 
</ul>
最新评论标签：

{m:comment_newest limit="10"}

可能存在的属性:

属性名------解释说明
limit ------限制条数，例如: limit="10" 默认值为20,此属性可选

范例：
<ul>
{loop $data $v}	
	<li>
		<span class="comment_article"><a href="{$v[url]}" title="{$v[title]}">{$v[title]}</a></span>
		<span class="comment_comment">{if $v['userid']}{$v[username]}{else}网友评论{/if}：{$v[content]}</span>
	</li>
{/loop} 
</ul>
