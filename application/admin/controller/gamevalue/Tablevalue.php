<?php
/**
 * 游戏数值管理
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/8/30
 */
namespace app\admin\controller\gamevalue;

use app\admin\model\gamevalue\TableSet;
use app\common\controller\Backend;
use think\Db;
use think\Config;

/**
 * 内容表
 *
 * @icon fa fa-circle-o
 */
class TableValue extends Backend
{

    /**
     * Archives模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['get_table_fields'];
    protected $pid;
    protected $fields = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\gamevalue\TableValue;
        $this->tableFieldsInit();
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

            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = [];
            if($total > 0){
                $_list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                foreach ((array)$_list as $row){
                    $data = json_decode($row['data'], true);
                    $data['id'] = $row['id'];
                    $list[] = $data;
                }
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        $modelList = \app\admin\model\Modelx::all();
        $this->view->assign('modelList', $modelList);
        return $this->view->fetch();
    }

    /**
     * 新增
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $input = input('post.');
            $data['pid'] = $this ->request->param('pid');
            $data['data'] = json_encode($input["row"]);
            if($this->model->save($data)){
                $this->success(__('Operation completed', ''));
            }else{
                $this->error(__('Operation failed', ''));
            }
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     *
     * @param mixed $ids
     */
    public function edit($ids = NULL)
    {
        if ($this->request->isPost()) {
            $input = input('post.');
            $data['data'] = json_encode($input["row"]);
            if($this->model->save($data, ['id' => $ids])){
                $this->success(__('Operation completed', ''));
            }else{
                $this->error(__('Operation failed', ''));
            }
        }
        $row = $this->model->get($ids);
        if (!$row){
            $this->error(__('No Results were found'));
        }
        $data = json_decode($row['data'], true);
        $data['id'] = $row['id'];
        $this->view->assign("row", $data);
        return $this->view->fetch();
    }

    /**
     * 删除
     * @param mixed $ids
     */
    public function del($ids = "")
    {
        return parent::del($ids);
    }

    /**
     * 导入
     */
    public function import()
    {
        Config::set('default_return_type', 'json');
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath)) {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        $fieldArr = [];
        foreach ($this->fields as $v) {
            $fieldArr[$v['name']] = $v['name'];
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $fields[] = $val;
            }
        }
        $insert = [];
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            $values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $values[] = is_null($val) ? '' : $val;
            }
            $row = [];
            $temp = array_combine($fields, $values);
            foreach ($temp as $k => $v) {
                if (isset($fieldArr[$k]) && $k !== '') {
                    $row[$fieldArr[$k]] = $v;
                }
            }
            if ($row) {
                $insert[] = [
                    'pid' => $this->pid,
                    'data' => json_encode($row),
                ];
            }
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }
        try {
            $this->model->where(['pid' => $this->pid])->delete();
            $this->model->saveAll($insert);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        // 删除导入文件
        @unlink($filePath);
        $this->success("导入成功");
    }

    /**
     * 游戏复位
     */
    public function reset()
    {
        $tableInfo = TableSet::where('game_id', '=', $this->gameInfo['game_id'])->where('id', '=', $this->pid)->find()->toArray();
        $result = $this->resetGameServerData('game', 'gamevalue', ['table' => $tableInfo['name']]);
        if($result['ret']){
            $this->success($tableInfo['title'].'['.$tableInfo['name'].']'."复位成功");
        }else{
            $this->error($result['msg']);
        }
    }

    /**
     * 表字段初试化
     */
    private function tableFieldsInit()
    {
        $tableList = [];
        if(empty($this->gameInfo['game_id'])){
            $this->error("请先选择游戏");
        }
        $all = collection(TableSet::where('game_id', '=', $this->gameInfo['game_id'])
            ->where('pid', '=', 0)
            ->order("weigh desc,id asc")->select())->toArray();
        foreach ($all as $row) {
            $state = ['opened' => true];
            $state['checkbox_disabled'] = true;
            $tableList[] = [
                'id'     => $row['id'],
                'parent' => '#',
                'text'   => $row['title'].'['.$row['name'].']',
                'type'   => 'list',
                'state'  => $state
            ];
        }
        $this->assignconfig('tableList', $tableList);

        $pid = $this->request->request("pid");
        $this->pid = !empty($pid)?$pid:(!empty($tableList[0]['id'])?$tableList[0]['id']:'');
        $columns = [];
        if($this->pid){
            $result = collection(Db::name('table_set')->where('pid', '=', $this->pid)->field('title,name,type')->select())->toArray();
            $columns[] = ['field' => 'state', 'checkbox' => true];
            foreach ((array)$result as $row){
                $columns[] = [
                    'field' => $row['name'],
                    'title' => $row['title'],
                ];
                $this->fields[] = $row;
            }
        }
        $this->assign('pid', $this->pid);
        $this->assign('fields', $this->fields);
        $this->assignconfig('pid', $this->pid);
        $this->assignconfig('columns', $columns);
    }

}
