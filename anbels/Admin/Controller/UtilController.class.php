<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class UtilController extends BaseController {
    
    public function get_location_children()
    {
        $code = I('_c',null);
        if(!$code){
            $this->ajaxReturn(array('flag' => 1,'data' => null));
        }
        
        $Location = M('MasterLocation');
        $children = $Location->where(array('active' => 0 , 'parent_code' => $code))->order('name')->select();
        
        $School = M('MasterSchool');
        $school = $School->join('anbels_master_location on anbels_master_location.id = anbels_master_school.location_id')
        ->where('anbels_master_school.active = 0 and anbels_master_location.code="'.$code.'"')
        ->field('anbels_master_school.id,anbels_master_school.name')
        ->select();
        
        // dump($school);
        
        $this->ajaxReturn(array('flag' => 0 ,'children' => $children  ,'school' => $school));
    }
    
    
}
    