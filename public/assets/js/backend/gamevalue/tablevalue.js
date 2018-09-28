define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'gamevalue/tablevalue/index',
                    add_url: 'gamevalue/tablevalue/add?pid='+Config.pid,
                    edit_url: 'gamevalue/tablevalue/edit?pid='+Config.pid,
                    del_url: 'gamevalue/tablevalue/del?pid='+Config.pid,
                    import_url: 'gamevalue/tablevalue/import?pid='+Config.pid
                }
            });

            var table = $("#table");

            var columns = Config.columns;
            columns.push({field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate});
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                queryParams: function (params) {
                    params.filter = JSON.stringify({pid:Config.pid});
                    return params;
                },
                pk: 'id',
                sortName: 'id',
                sortOrder: 'asc',
                columns: [columns]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            require(['jstree'], function () {
                $('#tabletree').on("changed.jstree", function (e, data) {
                    // console.log(data);
                    var pid = data.selected.join(",");
                    location.href = Config.moduleurl+"/"+$.fn.bootstrapTable.defaults.extend.index_url+"?pid="+pid;
                    return false;
                });
                $('#tabletree').jstree({
                    "themes": {
                        "stripes": true
                    },
                    "checkbox": {
                        "keep_selected_style": false,
                    },
                    "types": {
                        "list": {
                            "icon": "fa fa-list",
                        }
                    },
                    'plugins': ["types"],
                    "core": {
                        "multiple": true,
                        'check_callback': true,
                        "data": Config.tableList
                    }
                });
            });

            // 游戏复位
            $(document).on('click', '.btn-game-reset', function () {
                Layer.confirm(__('Confirm game reset?'),{icon:3, title:'温馨提示', offset: '30%'},function(index){
                    var index = Layer.load(0, {offset: '40%'});
                    setTimeout(function(){
                        $.ajax({
                            url: 'gamevalue/tablevalue/reset?pid='+Config.pid,
                            type: 'get',
                            dataType: 'json',
                            success: function(ret){
                                Layer.closeAll();
                                if(ret.code == 1){
                                    Toastr.success(ret.msg);
                                }else{
                                    Toastr.error(ret.msg);
                                }
                            },
                            error: function (ret) {
                                Layer.close(index);
                                Toastr.error(ret.msg);
                            }
                        });
                    },1000);
                });
            });
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