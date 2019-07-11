<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//导入核心配置类
use think\Config;
use think\Loader;
use lib\Ucpaas;


// //导入发送邮件核心类
// // $to 接受方 $title 邮件主题 $content 内容
// function sendMail($to,$title,$content){
// 	$mail=new \Org\Util\PHPMailer();
// 	//初始化参数
// 		//设置字符集
// 	$mail->CharSet="utf-8";
// 	//设置采用SMTP方式发送邮件
// 	$mail->IsSMTP();
// 	//设置邮件服务器地址
// 	$mail->Host=Config::get('mailarr.host');//获取配置文件信息 
// 	//设置邮件服务器的端口 默认的是25  如果需要谷歌邮箱的话 443 端口号
// 	$mail->Port=25;
// 	//设置发件人的邮箱地址
// 	$mail->From=Config::get('mailarr.username'); //
// 	// $mail->FromName='我';//
// 	//设置SMTP是否需要密码验证
// 	$mail->SMTPAuth=true;
// 	//发送方
// 	$mail->Username=Config::get('mailarr.username');
// 	$mail->Password=Config::get('mailarr.password');//客户端的授权密码
// 	//发送邮件的主题
// 	$mail->Subject=$title;
// 	//内容类型 文本型
// 	$mail->AltBody="text/html";
// 	//发送的内容
// 	$mail->Body=$content;
// 	//设置内容是否为html格式
// 	$mail->IsHTML(true);
// 	//设置接收方
// 	$mail->AddAddress(trim($to));
// 	if(!$mail->Send()){
// 		return false;
// 		// echo "失败".$mail->ErrorInfo;
// 	}else{
// 		return true;
// 	}
// }
//导入系统类 Loader

//短信校验码获取
function funcs($p){
	//导入三方类
	// Vendor("lib.Ucpaas");
	//初始化必填
	//填写在开发者控制台首页上的Account Sid
	$options['accountsid']='14692f65b1532b9994def4f89e46c024';
	//填写在开发者控制台首页上的Auth Token
	$options['token']='d9b32309e3413c3dd2ae4b1d408b254f';

	//初始化 $options必填
	$ucpass = new lib\Ucpaas($options);

	$appid = "d9189ab47f1c43aa9cc7da46ef229b4b";	//应用的ID，可在开发者控制台内的短信产品下查看
	//模板id
	$templateid = "399707";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
	//校验码
	$param =rand(1,10000); //多个参数使用英文逗号隔开（如：param=“a,b,c”），如为参数则留空
	//存储在cookie  60秒后过期
	setcookie("fcode",$param,time()+60);
	//接受的手机号
	$mobile = $p;
	$uid = "";

	//70字内（含70字）计一条，超过70字，按67字/条计费，超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。

	echo $ucpass->SendSms($appid,$templateid,$param,$mobile,$uid);
}

//支付宝支付接口调用
function pay($ordercode,$name,$fee,$des){
	require_once("alipay.config.php");
	require_once("lib/alipay_submit.class.php");

	/**************************请求参数**************************/
    //商户订单号，商户网站订单系统中唯一订单号，必填
    $out_trade_no = $ordercode;

    //订单名称，必填
    $subject = $name;

    //付款金额，必填 单价乘以数量
    $total_fee = $fee;

    //商品描述，可空
    $body = $des;
	/************************************************************/
	//构造要请求的参数数组，无需改动
	$parameter = array(
		"service"       => $alipay_config['service'],
		"partner"       => $alipay_config['partner'],
		"seller_id"  => $alipay_config['seller_id'],
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> $alipay_config['notify_url'],
		"return_url"	=> $alipay_config['return_url'],
		
		"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
		"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
        //如"参数名"=>"参数值"	
	);

	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	echo $html_text;
}