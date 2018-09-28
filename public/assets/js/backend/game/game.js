define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'game/game/index',
                    add_url: 'game/game/add',
                    edit_url: 'game/game/edit',
                    del_url: 'game/game/del',
                    multi_url: 'game/game/multi',
                    table: 'game',
                }
            });

            var table = $("#table");

            //在表格内容渲染完成后回调的事件
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-editone", this)
                    .off("click")
                    .removeClass("btn-editone")
                    .addClass("btn-addtabs")
                    .prop("title", __('Edit'));
            });
            //当双击单元格时
            table.on('dbl-click-row.bs.table', function (e, row, element, field) {
                $(".btn-addtabs", element).trigger("click");
            });

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
                        {field: 'token', title: __('Token')},
                        {field: 'game_id', title: __('Game_id')},
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
        },
        add: function () {
            Controller.api.common();
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.common();
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            common: function () {
                //追加
                $(document).on('click', '.btn-append', function () {
                    var dd = $('dd:eq(1)', '.field-list');
                    var new_dd = dd.clone();
                    var size = $('.field-list-content').size();
                    new_dd.find('input:eq(0)').attr('name', 'row[player_search_config]['+ size +'][name]');
                    new_dd.find('input:eq(1)').attr('name', 'row[player_search_config]['+ size +'][field]');
                    if(size <= 0){
                        new_dd = '<dd class="form-inline timespace">\n' +
                            '<input class="form-control" name="row[player_search_config][0][name]" type="text" value="">\n' +
                            '<input class="form-control" name="row[player_search_config][0][field]" type="text" value="">\n' +
                            '<span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>\n' +
                            '</dd>';
                    }
                    $(this).closest('dd').before(new_dd);
                    Controller.api.rendertimepicker($('.field-list'))
                });

                //移除
                $(document).on('click', '.btn-remove', function () {
                    $(this).closest('dd').remove();
                });
            }
        }
    };
    return Controller;
});