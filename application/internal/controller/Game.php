<?php
/**
 * 游戏接口
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2016/6/30
 */
namespace app\internal\controller;

use app\common\controller\Internal;
use think\Db;

class Game extends Internal
{

    /**
     * 游戏数值接口
     *
     * @param string table
     * @return json
     */
    public function gamevalue()
    {
        $data = $this->input['data'];
        $validate = $this->validate($data, "game.gamevalue");
        if($validate !== true){
            return $this->error("请求参数缺省", ['debug' => $validate], -7);
        }
        $where = [
            'game_id' => $this->gameInfo['game_id'],
            'name' => $data['table'],
        ];
        $table = Db::name('table_set')->where($where)->find();
        if($table){
            $list = [];
            $value = Db::name('table_value')->where('pid', '=', $table['id'])->select();
            foreach ($value as $row){
                $list[] = json_decode($row['data'], true);
            }
            $this->success("success", $list);
        }else{
            $this->error("找不到此表配置信息");
        }
    }

    /**
     * 游戏开关接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function gameswitch()
    {
        $where = [
            'c.game_id' => $this->gameInfo['game_id'],
            's.game_id' => $this->gameInfo['game_id'],
        ];

        //开关列表
        $arrSwitch = Db::name('game_switch')
            ->alias('w')
            ->field('w.*,c.channel_key,s.scene_key')
            ->join('game_channel c', 'w.channel_id=c.id')
            ->join('game_scene s', 'w.scene_id=s.id')
            ->where($where)
            ->order('weigh', 'desc')
            ->select();

        if(!$arrSwitch){
            $this->error(__('No results were found'));
        }

        $data = [];
        foreach ($arrSwitch as $switch){
            $subData = [
                'channel_key' => $switch['channel_key'],
                'scene_key' => $switch['scene_key'],
                'state' => $switch['status'],
                'vip_level' => $switch['vip_level'],
                'timespace' => json_decode($switch['timespace'], true),
                'url_list' => !empty($arrScene[$switch['scene_id']]['scene_url']) ? explode('|', $arrScene[$switch['scene_id']]['scene_url']):[],
            ];
            array_push($data, $subData);
        }

        $this->success(__('success'), $data);
    }

}
