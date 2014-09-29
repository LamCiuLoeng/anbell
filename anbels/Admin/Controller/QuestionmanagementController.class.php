<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

class QuestionmanagementController extends BaseController {
    public function index(){
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
			
		$master_question = M('master_question')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		//p($master_question);
		$this->assign('master_question',$master_question);// 赋值数据集
		$count = M('master_question')->where('active=0')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		//p($show);
		$this->assign('page',$show);// 赋值分页输出
		$this->display('question_list');
		die();
		$master_question=M('master_question')->where('active=0')->select();
		$this->assign('master_question',$master_question);
        $this->display('question_list');
	}

	public function question_add(){
        $this->display();
	}

	public function question_add_handle(){
		
		$master_question = M("master_question"); // 实例化User对象
		$data['course_id'] = I('post.course');
		$data['content'] = I('post.question_name');
		$data['correct_answer'] = I('post.correct_answer');
		$data['answer01'] = I('post.answer01');
		$data['answer01_content'] = I('post.answer_content01');
		$data['answer02'] = I('post.answer02');
		$data['answer02_content'] = I('post.answer_content02');
		$data['answer03'] = I('post.answer03');
		$data['answer03_content'] = I('post.answer_content03');
		$data['answer04'] = I('post.answer04');
		$data['answer04_content'] = I('post.answer_content04');
		$data['active'] = 0;
		$data['create_by_id'] = session('user_id');
		$data['create_time'] = mynow();
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		
		if($master_question->add($data))
		{
			$this->success('题库添加成功！',U('questionmanagement/index'));
		} 
		else 
		{
			$this->error('题库添加失败，请重试...');	
		}
		 
		
	}
	
	public function question_edit(){
		if(I('post.checkbox'))
		{
			$checkbox_array=I('post.checkbox');
			$map['id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$master_question=M('master_question')->where($map)->select();
			$this->assign('master_question',$master_question);
			$this->display();
		} 
		else 
		{
			$this->error('请选择所要修改的题目，重试...','',2);	
		}
	}
	public function question_edit_handle(){
		$master_question = M("master_question"); // 实例化User对象
		$map['id'] = I('post.id');
		$data['course_id'] = I('post.course');
		$data['content'] = I('post.question_name');
		$data['correct_answer'] = I('post.correct_answer');
		$data['answer01'] = I('post.answer01');
		$data['answer01_content'] = I('post.answer_content01');
		$data['answer02'] = I('post.answer02');
		$data['answer02_content'] = I('post.answer_content02');
		$data['answer03'] = I('post.answer03');
		$data['answer03_content'] = I('post.answer_content03');
		$data['answer04'] = I('post.answer04');
		$data['answer04_content'] = I('post.answer_content04');
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		if($master_question->where($map)->setField($data))
		{
			$this->success('修改成功！',U('questionmanagement/index'));
		} 
		else 
		{
			$this->error('修改失败，请重试...');	
		}
	}
	
	public function question_list_delete_handle(){
		$checkbox_array=I('post.checkbox');
		$master_question = M("master_question"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($master_question->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('questionmanagement/index'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的题目...');	
		}
	}
	
    public function imp()
    {
        $this->show();
    }
	
    public function imp_handle()
    {
        $config = array(    
                    'rootPath'   =>    './Public/',
                    'savePath'   =>    'Upload/',    
                    'saveName'   =>    array('uniqid',''),    
                    //'exts'       =>    array('csv', 'xls', 'xlsx'),    
                    'autoSub'    =>    true,    
                    'subName'    =>    array('date','Ymd','time'),
                 );
        $upload = new \Think\Upload($config);// 实例化上传类
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }    
        
        $info   =   $upload->uploadOne($_FILES["xls"]);  
        if($info) {
            // 上传成功      
            $M = M("MasterQuestion");         
            $path = './Public/'.$info['savepath'].$info['savename'];
            $data = readXLS($path);
            // dump($data);
            $index = 0;
            foreach ($data as $row) {
                $index += 1;
                if($index == 1){ continue ;} //excel header
                $tmp = mydto();
                $tmp['course_id'] = $row["F"];
                $tmp['content'] = $row["G"];
                $tmp['correct_answer'] = $row["H"];
                $tmp['answer01'] = $row["I"];
                $tmp['answer01_content'] = $row["J"];
                $tmp['answer02'] = $row["K"];
                $tmp['answer02_content'] = $row["L"];
                $tmp['answer03'] = $row["M"];
                $tmp['answer03_content'] = $row["N"];
                $tmp['answer04'] = $row["O"];
                $tmp['answer04_content'] = $row["P"];
                $M->create($tmp);
                $M->add();
            }
        }else{
            $this->error($upload->getError()); 
        }
        
        $this->success('批量导入成功！',U('questionmanagement/index'));
    }
    
}