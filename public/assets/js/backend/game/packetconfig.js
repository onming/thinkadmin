define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'game/packet_config/index',
                    add_url: 'game/packet_config/add',
                    edit_url: 'game/packet_config/edit',
                    del_url: 'game/packet_config/del',
                    multi_url: 'game/packet_config/multi',
                    table: 'packet_config',
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
                        {field: 'type', title: __('Type'), searchList: {"1": __('Phone charge'), "2": __('Shopping card')}, formatter: Table.api.formatter.label},
                        {field: 'money', title: __('Money')},
                        {field: 'exchange_money', title: __('Exchange money')},
                        {field: 'content', title: __('Content')},
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

            //重置数据
            $(document).on('click', '.btn-reset', function () {
                Layer.confirm('确定重置服务器数据？',{icon:3, title:'温馨提示', offset: '30%'},function(index){
                    var index = Layer.load(0, {offset: '40%'});
                    setTimeout(function(){
                        $.ajax({
                            type: 'post',
                            url: 'game/packet_config/reset',
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
            }
        }
    };
    return Controller;
});