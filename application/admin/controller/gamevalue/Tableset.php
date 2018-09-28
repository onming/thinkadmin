<?php
/**
 * 游戏表管理
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/8/30
 */
namespace app\admin\controller\gamevalue;

use app\common\controller\Backend;
use app\common\model\Config;

class Tableset extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\gamevalue\TableSet;
    }

    public function typelist()
    {
        return $regexList = Config::getDataTypeList();
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if(empty($this->gameInfo['game_id'])){
                return $this->error("请选择游戏");
            }
            $total = $this->model
                ->where($where)
                ->where('game_id', '=', $this->gameInfo['game_id'])
                ->where('pid', '=', 0)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('game_id', '=', $this->gameInfo['game_id'])
                ->where('pid', '=', 0)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $arrPostData = input('post.');
            $data = [
                'pid' => 0,
                'name' => $arrPostData['row']['name']
            ];
            $total = $this->model->where($data)->count();
            //判断数据库表明是否存在
            if($total != 0){
                $this->error(__('Add failed', ''));
            }
            $data = [
                'pid' => 0,
                'name' => $arrPostData['row']['name'],
                'title' => $arrPostData['row']['title']
            ];
            if(empty($this->gameInfo['game_id'])){
                return $this->error("请选择游戏");
            }
            $data['game_id'] = $this->gameInfo['game_id'];
            //生成表名
            $result = $this->model->save($data);
            if($result){
                //取表名自增长id
                $id = $this->model->where($data)->column('id')[0];
                $newArr = array();
                if(isset($arrPostData['rows'])){
                    //$arrPostData['rows'] --> 字段数组
                    foreach($arrPostData['rows'] as $key => &$value){
                        $value['pid'] = $id;
                        //将各个数组的字段名提取放入新数组
                        array_push($newArr,$value['name']);
                    }
                    //判断字符名是否重复，array_unique --> 去除数组重复的值
                    if (count($newArr) != count(array_unique($newArr))) {
                        $this->error(__('Field repetition', ''));
                    }else{
                        $this->model->insertAll($arrPostData['rows'],true);
                    }
                }
            }
            $this->success(__('Operation completed', ''));
        }
        return $this->view->fetch();
    }
    /**
     * 编辑
     */
    public function edit($ids = '')
    {
        if ($this->request->isPost()) {
            $arrPostData = input('post.');
            $data = [
                'name' => $arrPostData['row']['name'],
                'title' => $arrPostData['row']['title']
            ];
            //更新表名
            $data['updatetime'] = time();
            $this->model->where('id',$arrPostData['row']['id'])->update($data);
            //删除该表底下的所有字段
            $this->model->where('pid',$arrPostData['row']['id'])->delete();
            $newArr = array();
            //判断用户是否添加字段数据。 $arrPostData['rows'] --> 字段数组
            if(isset($arrPostData['rows'])) {
                foreach ($arrPostData['rows'] as $key => &$value) {
                    $value['pid'] = $arrPostData['row']['id'];
                    //将各个数组的字段名提取放入新数组
                    array_push($newArr, $value['name']);
                }
                //判断字符名是否重复，array_unique --> 去除数组重复的值
                if (count($newArr) != count(array_unique($newArr))) {
                    $this->error(__('Field repetition', ''));
                } else {
                    $this->model->insertAll($arrPostData['rows'], true);
                }
            }
            $this->success(__('Operation completed', ''));
        }
        $where = [
            'pid' => $ids
        ];
        $row = $this->model->get($ids);
        $fieldArr = $this->model->all($where);
        if (!$row){
            $this->error(__('No Results were found'));
        }
        $this->view->assign("typelist",$this->typelist());//获取select下拉选项
        $this->view->assign("fieldArr", $fieldArr);//字段数据
        $this->view->assign("row", $row);//表名数据
        return $this->view->fetch();
    }

}
