define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'game/blackgold/index',
                    add_url: 'game/blackgold/add',
                    edit_url: 'game/blackgold/edit',
                    del_url: 'game/blackgold/del',
                    multi_url: 'game/blackgold/multi',
                    table: 'game_vip',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
                        // {field: 'env', title: __('Env'), searchList: {"production":__('Env production'),"stress":__('Env stress'),"test":__('Env test'),"dev":__('Env dev'),"work":__('Env work')}, formatter: Table.api.formatter.normal},
                        {field: 'level', title: __('Level')},
                        {field: 'discount', title: __('Discount')},
                        {field: 'is_present', title: __('Present'), searchList: {"0":__('Close'),"1":__('Open')}, formatter: Table.api.formatter.flag},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            
            // 绑定TAB事件
            $('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var field = $(this).closest("ul").data("field");
                var value = $(this).data("value");
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    var filter = {};
                    if (value !== '') {
                        filter[field] = value;
                    }
                    params.filter = JSON.stringify(filter);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            //设置玩家vip等级
            $(document).on("click", ".btn-setting", function () {
                Layer.open({
                    content: Template("searchUsertpl", {}),
                    area: '50%',
                    offset: '20%',
                    title: __('Set_user_blackgold'),
                    btn: [],
                    shade: 0,
                });
            });
            //搜索按钮绑定
            $(document).on('click', '#search', function () {
                var player_id = $('#search_form input[name=search]').val();
                var reg = /^\d+$/;
                var params = $('#search_form').serialize();
                var index = Layer.load(0, {offset: '30%'});
                if(!reg.test(player_id)){
                    Layer.close(index);
                    Toastr.error('搜索内容格式错误');
                    return;
                }
                $.ajax({
                    url: 'game/blackgold/getUserOptions',
                    type: 'post',
                    data: params,
                    dataType: 'json',
                    success: function(res){
                        Layer.close(index);
                        if(res.code == 1){
                            $('#table_form').html(res.data);
                            //把搜索的添加到缓存里面
                            //var player_id = [];
                            //player_id.push(params)
                            // localStorage.setItem("search_player", )
                        }else{
                            // Layer.msg(res.msg, {offset: '30%', icon: 7});
                            Toastr.error(res.msg)
                            $('#setting_submit').attr('disabled', true);
                        }
                    },
                    error: function (res) {
                        Layer.close(index);
                        Toastr.error(res.msg);
                        $('#setting_submit').attr('disabled', true);
                    }
                });
            });

            //保存设置
            $(document).on('click', '#setting_submit', function () {
                var params = $('#setting-form').serialize();
                var index = Layer.load(0);
                $.ajax({
                    url: 'game/blackgold/setUserOptions',
                    type: 'post',
                    data: params,
                    dataType: 'json',
                    success: function(ret){
                        Layer.close(index);
                        if(ret.code == 1){
                            Toastr.success(ret.msg);
                            //Layer.closeAll();
                        }else{
                            // Layer.msg(res.msg, {offset: '30%', icon: 7});
                            Toastr.error(ret.msg);
                        }
                    },
                    error: function (ret) {
                        Layer.close(index);
                        Toastr.error(ret.msg)
                    }
                });
            });

            //重置数据
            $(document).on('click', '.btn-reset', function () {
                Layer.confirm('确定重置服务器数据？',{icon:3, title:'温馨提示', offset: '30%'},function(index){
                    var index = Layer.load(0, {offset: '40%'});
                    setTimeout(function(){
                        $.ajax({
                            type: 'post',
                            url: 'game/blackgold/reset',
                            dataType: 'json',
                            success: function (ret) {
                                Layer.closeAll();
                                if(ret.code == 1){
                                    Toastr.success(ret.msg);
                                }else{
                                    Toastr.error(ret.msg);
                                }
                            },
                            error: function (ret) {
                                Layer.close(index);
                                Toastr.error(ret.msg)
                            }
                        });
                    },1000);
                })
            })
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            userinfo: function () {
                
            }
        },
    };
    return Controller;
});