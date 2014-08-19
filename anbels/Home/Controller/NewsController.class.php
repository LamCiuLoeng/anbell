<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function index(){
		$map['category'] = '新闻动态';
		$web_news=M('web_news')->where($map)->select();
		$this->assign('web_news',$web_news);
		$this->display('news');
    }
	
	public function news_page(){
		$map['id'] = I('id');
		$web_news=M('web_news')->where($map)->find();
		$this->assign('web_news',$web_news);
		$this->display();
    }

}