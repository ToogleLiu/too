<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>首页</title>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>

<body>
	
	{{if $userinfo neq null}}
		{{foreach from=$userinfo key=key item=value}}
			<p>{{$key}}:{{$value}}</p>
		{{/foreach}}
	{{else}}
		<p>nothing.</p>
	{{/if}}
</body>
</html>
