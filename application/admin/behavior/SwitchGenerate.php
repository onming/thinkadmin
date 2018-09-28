<?php
/**
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/14
 */

namespace app\admin\behavior;


use app\admin\model\game\Channel;
use app\admin\model\game\GameSwitch;
use app\admin\model\game\Scene;

class SwitchGenerate
{

    public function channelAfter(&$params)
    {
        //场景列表
        $arrScene = Scene::all(['status'=>1, 'game_id'=>$params['game_id']]);
        if(!$arrScene){
            return false;
        }
        $arrSceneId = array_column(collection($arrScene)->toArray(), 'id');

        $switchModel = new GameSwitch();
        //已添加的开关
        $arrHasScene = $switchModel->where('channel_id', $params['id'])->column('scene_id');

        if($arrHasScene){
            $arrSceneId = array_diff($arrSceneId, $arrHasScene);
        }

        $result = true;
        if($params['action'] == 'add'){

            if($arrSceneId){
                $insertData = [];
                foreach ($arrSceneId as $value){
                    $subData = [
                        'channel_id' => $params['id'],
                        'scene_id' => $value,
                        'vip_level' => 0,
                        'timespace' => '[]',
                        'weigh' => 50,
                        'status' => 0,
                    ];

                    array_push($insertData, $subData);
                }

                $result = $switchModel->saveAll($insertData);
            }
        }

        if($params['action'] == 'del'){
            $result = $switchModel->where('channel_id', $params['id'])->delete();
        }

        return $result;

    }

    public function sceneAfter(&$params)
    {
        //渠道列表
        $arrChannel = Channel::all(['status'=>1, 'game_id'=>$params['game_id'], 'pid'=>0]);
        if(!$arrChannel){
            return false;
        }
        $arrChannelId = array_column(collection($arrChannel)->toArray(), 'id');


        $switchModel = new GameSwitch();
        //已添加的开关
        $arrHasChannel = $switchModel->where('scene_id', $params['id'])->column('channel_id');

        if($arrHasChannel){
            $arrChannelId = array_diff($arrChannelId, $arrHasChannel);
        }

        $result = true;
        if($params['action'] == 'add'){

            if($arrChannelId){
                $insertData = [];
                foreach ($arrChannelId as $value){
                    $subData = [
                        'channel_id' => $value,
                        'scene_id' => $params['id'],
                        'vip_level' => 0,
                        'timespace' => '[]',
                        'weigh' => 50,
                        'status' => 0,
                    ];

                    array_push($insertData, $subData);
                }

                $result = $switchModel->saveAll($insertData);
            }
        }

        if($params['action'] == 'del'){
            $result = $switchModel->where('scene_id', $params['id'])->delete();
        }

        return $result;
    }

    public function switchSync(&$params)
    {
        //查询渠道和场景列表
        $arrChannel = Channel::all(['status'=>1, 'game_id'=>$params['game_id'], 'pid'=>0]);
        $arrScene = Scene::all(['status'=>1, 'game_id'=>$params['game_id']]);
        if($arrChannel){
            $arrChannelId = array_column(collection($arrChannel)->toArray(), 'id');
        }

        if($arrScene){
            $arrSceneId = array_column(collection($arrScene)->toArray(), 'id');
        }

        $switchModel = new GameSwitch();

        //已添加的开关
        $arrSwitch = $switchModel->where(['channel_id'=>['in', $arrChannelId], 'scene_id'=>['in', $arrSceneId]])
            ->field('id,channel_id,scene_id')
            ->select();

        $arrNewSwitch = array_under_reset(collection($arrSwitch)->toArray(), 'channel_id', 2);

        $insertData = [];
        foreach ($arrChannel as $channel){
            foreach ($arrScene as $scene){
                if(array_key_exists($channel['id'], $arrNewSwitch)){
                    $arrHasScene = array_unique(array_column($arrNewSwitch[$channel['id']], 'scene_id'));
                    if(in_array($scene['id'], $arrHasScene)){
                        continue;
                    }
                }
                $subData = [
                    'channel_id' => $channel['id'],
                    'scene_id' => $scene['id'],
                    'vip_level' => 0,
                    'timespace' => '[]',
                    'weigh' => 50,
                    'status' => 0,
                ];
                array_push($insertData, $subData);
            }
        }

        $result = $switchModel->saveAll($insertData);

        if($result === false){
            return ['code'=>0, 'msg'=>__('Operation failed')];
        }
        //halt(collection($arrSwitch)->toArray());
        return ['code'=>1, 'msg'=>__('Operation completed')];

    }
}
