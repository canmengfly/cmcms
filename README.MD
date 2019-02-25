![](https://main.qcloudimg.com/raw/fe3bef880cbf1d47036a948eff96ccc4.png)



轻量明快，设计简洁，遵循 MIT 的 PHP+MySql 二次开源CMS系统



安装方法：
运行 “你的网址/install.php”，按照安装提示说明进行程序安装。

第一次登陆后台后：
请点击：内容管理->批量更新URL->更新栏目URL和更新内容URL



采用MVC设计模式开发,基于模块和操作的方式进行访问，采用单一入口模式进行项目部署和访问，无论访问任何一个模块或者功能，只有一个统一的入口。

如果在二次开发，建议开启APP_DEBUG，在根目录下index.php文件的12行。

一、基本目录结构

```
..../              根目录
..../application   全站应用目录
..../cache         缓存目录[必须可写入]
..../common        全站公共目录[必须可写入]
..../uploads       默认上传目录[必须可写入]
..../yzmphp        程序核心目录[不建议修改]
..../index.php     程序单一入口文件
..../.htaccess     Apache伪静态文件[如您不是Apache软件，可根据此规则重写伪静态]
..../nginx.conf    Nginx下伪静态文件
```




application目录下每一个文件夹都是一个单独的模块（module）

每个模块下都有四个文件夹（common、controller、model、view）一个文件（index.html），这几个文件时必须有的，新建模块时也一定要有这几个文件。

以admin模块举例：

```
application

---admin
------common（模块公共目录）
------controller（模块控制器目录）
------model（模块模型目录）
------view（模块视图目录）
```



