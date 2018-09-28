<?php

namespace app\admin\model\game;

use think\Model;

class GameSwitch extends Model
{
    // 表名
    protected $name = 'game_switch';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            //$pk = $row->getPk();
            //$row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    /**
     * 时间范围获取器
     * @param $value
     * @return mixed|string
     */
    public function getTimespaceAttr($value)
    {
        $action = request()->action();
        $value = json_decode($value, true);
        if($action == 'index'){
            $arrTimeRange = [];
            if(empty($value)){
                return '-';
            }
            foreach ($value as $item){
                array_push($arrTimeRange, $item['start_time'].'-'.$item['end_time']);
            }
            $value = mb_strcut(implode('|', $arrTimeRange), 0, 20).'...';
        }
        return $value;
    }

    /**
     * 渠道关联
     * @return \think\model\relation\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo('app\admin\model\game\Channel');
    }

    /**
     * 场景关联
     * @return \think\model\relation\BelongsTo
     */
    public function scene()
    {
        return $this->belongsTo('app\admin\model\game\Scene');
    }

    /**
     * 开关列表
     */
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }

    /**
     * 渠道列表
     */
    public function getChannelList($game_id)
    {
        //渠道列表
        $arrChannel = collection(
            Channel::where(['game_id'=>$game_id, 'status'=>1])
                ->field('id,pid,channel_name,status')
                ->order('weigh', 'desc')
                ->select()
        )->toArray();

        $channelList = [];
        foreach ($arrChannel as $key => $value){
            $channel = [
                'id' => $value['id'],
                'parent' => $value['pid'] ? $value['pid'] : '#',
                'icon'   => $value['pid'] ? 'fa fa-outdent' : '',
                'text'   => __($value['channel_name']),
                'state'  => [
                    'opened' => false,
                ]
            ];
            array_push($channelList, $channel);
        }
        return [$arrChannel, $channelList];
    }

    /**
     * 场景列表，排除已添加的场景
     * @param $game_id 游戏id
     * @param null $channel_id 渠道id
     * @param int $scene_id 排除禁用的场景id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSceneList($game_id, $channel_id=null, $scene_id = 0)
    {
        //场景
        $arrScene = collection(
            Scene::where(['game_id'=>$game_id, 'status' => 1])
                ->order('weigh', 'desc')
                ->select()
        )->toArray();

        array_walk($arrScene, function (&$value){
            $value['disabled'] = false;
        });

        if($arrScene && !is_null($channel_id)){
            $arrSceneId = array_column($arrScene, 'id');
            $arrGameSwitch = $this->where(['scene_id' => ['in', $arrSceneId], 'channel_id'=>$channel_id])->column('scene_id');
            if($arrGameSwitch){
                foreach ($arrGameSwitch as $val){
                    if(in_array($val, $arrSceneId)){
                        array_walk($arrScene, function(&$value) use ($val, $scene_id){
                            if($value['id'] == $val && $value['id'] != $scene_id){
                                $value['disabled'] = true;
                            }
                        });
                    }
                }
            }
        }

        return $arrScene;
    }

    /**
     * vip列表
     */
    public function getVipList()
    {
        $arrVip = [
            ['id'=>1, 'name'=>'VIP1'],
            ['id'=>2, 'name'=>'VIP2'],
            ['id'=>3, 'name'=>'VIP3'],
            ['id'=>4, 'name'=>'VIP4'],
            ['id'=>5, 'name'=>'VIP5'],
            ['id'=>6, 'name'=>'VIP6'],
            ['id'=>7, 'name'=>'VIP7'],
            ['id'=>8, 'name'=>'VIP8'],
            ['id'=>9, 'name'=>'VIP9'],
            ['id'=>10, 'name'=>'VIP10'],
        ];
        return $arrVip;
    }



}
