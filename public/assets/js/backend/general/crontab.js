define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init();
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var panel = $($(this).attr("href"));
                console.log(panel,this,panel.attr("id"));
                if (panel.size() > 0) {
                    Controller.table[panel.attr("id")].call(this);
                    $(this).on('click', function (e) {
                        $($(this).attr("href")).find(".btn-refresh").trigger("click");
                    });
                }
                //移除绑定的事件
                $(this).unbind('shown.bs.tab');
            });
            // 必须默认触发shown.bs.tab事件
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");
        },
        table: {
            one: function () {
                // 表格1
                var table = $("#table");
                table.bootstrapTable({
                    url: 'general/crontab/index',
                    toolbar: '#toolbar',
                    pk:'id',
                    sortName: 'id',

                    extend: {
                        index_url: 'general/crontab/index',
                        add_url: 'general/crontab/add',
                        edit_url: 'general/crontab/edit',
                        multi_url: 'general/crontab/multi_url',
                        table: 'crontab',
                    },
                    columns: [
                        [
                            {field: 'state', checkbox: true},
                            {field: 'id', title: 'ID'},
                            {field: 'title', title: __('Title')},
                            {field: 'maximums', title: __('Maximums'), formatter: Controller.api.formatter.maximums},
                            {field: 'executes', title: __('Executes')},
                            {field: 'begintime', title: __('Begin time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                            {field: 'endtime', title: __('End time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                            {field: 'nexttime', title: __('Next execute time'), formatter: Table.api.formatter.datetime, operate: false},
                            {field: 'executetime', title: __('Execute time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                            {field: 'weigh', title: __('Weigh')},
                            {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                buttons: [
                                    {name: '', text: '', title: '立即执行', icon: 'fa fa-play-circle', classname: 'btn btn-xs btn-primary  btn-ajax ', url: '/addons/crontab/Manualtask/index'},
                                ],
                                table: table,
                                events: Table.api.events.operate,
                                formatter: Table.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(table);
            },
            two: function () {
                // 表格2
                var table2 = $("#table2");
                table2.bootstrapTable({
                    url: 'general/crontab/log_index',
                    pk:'_id',
                    sortName: '_id',
                    search: false,
                    clickToSelect: true, //是否启用点击选中
                    dblClickToEdit: true,
                    extend: {
                        index_url: 'general/crontab/log_index',
                        edit_url: 'general/crontab/log_check',
                        table: 'crontab',
                    },
                    toolbar: '#toolbar2',
                    columns: [
                        [
                            {field: 'state', checkbox: true},
                            {field: 'module', title: __('module')},
                            {field: 'action', title: __('action')},
                            {field: 'msg', title: __('msg'), operate: false},//operate: false不参与搜索显示
                            {
                                field: 'time', title: __('time'),
                                formatter: Table.api.formatter.datetime,
                                operate: 'RANGE',
                                addclass: 'datetimerange',
                                sortable: true
                            },
                            {
                                field: 'status', title: __('code'),
                                searchList: {"1":'正常',"2":'异常'},//设置状态显示的字段
                                formatter: Table.api.formatter.status,
                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: table2,
                                events: Table.api.events.operate, formatter: Controller.api.formatter.operate//改变编辑图标样式
                            }
                        ]
                    ],

                });
                // 为表格2绑定事件
                Table.api.bindevent(table2);
            }
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
            formatter: {
                maximums: function (value, row, index) {
                    return value === 0 ? __('No limit') : value;
                },
                operate: function (value, row, index) {
                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);

                    if (options.extend.edit_url !== '') {
                        buttons.push({
                            name: 'edit',
                            icon: 'fa fa-eye',
                            title: '查看',
                            extend: 'data-toggle="tooltip"',
                            classname: 'btn btn-xs btn-info btn-editone',
                            url: options.extend.edit_url
                        });
                    }
                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                }
            }

        }
    };
    return Controller;
});
