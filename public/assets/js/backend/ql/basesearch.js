define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($,undefined,Backend,Table, Form) {

    var Controller = {
        playwarnum: function () {
            // 初始化表格参数配置
            Table.api.init();
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var panel = $($(this).attr("href"));
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
            FightAgainstLandlords: function () {
                // 表格1
                var table = $("#table");
                table.bootstrapTable({
                    url: 'ql/base_search/playwarnum?gameId=16',
                    extend: {
                        index_url: 'ql/basesearch/playwarnum',
                        close_url: 'ql/basesearch/djzClose',
                        create_url:'ql/basesearch/createClub',
                        table: 'basesearch',
                    },
                    toolbar: '#toolbar',
                    search: false,
                    // leftFixedColumns:true,
                    // leftFixedNumber:2,
                    // rightFixedColumns: true,
                    // rightFixedNumber: 1,
                    columns: [
                        [
                            {
                                field: 'AddTime',
                                title: __('Start time'),
                                formatter: Table.api.formatter.datetime,
                                operate: 'RANGE',
                                addclass: 'datetimerange',
                                visible: false
                            },
                            {field: 'PID', title:__('Id')}
                            , {field: 'NickName', title:__('Nick'),operate: false,}
                            , {field: 'PrimaryGold', title: __('Primary gold coin'), operate: false}
                            , {field: 'IntermediateGold', title: __('Intermediate gold coin'), operate: false}
                            , {field: 'AdvancedGold', title: __('Advanced gold coin'), operate: false}
                            , {field: 'PrimaryLz', title: __('Primary Lai'), operate: false}
                            , {field: 'IntermediateLz', title: __('Intermediate rice'), operate: false}
                            , {field: 'SeniorLz', title: __('Senior Lai'), operate: false}
                            , {field: 'RoutineFree', title: __('Free of charge'), operate: false}
                            , {field: 'RoutineZj', title: __('Regular intermediate'), operate: false}
                            , {field: 'RoutineGj', title: __('Conventional advanced'), operate: false}
                            , {field: 'LoopFree', title: __('Free circulation'), operate: false}
                            , {field: 'LoopZj', title: __('Intermediate cycle'), operate: false}
                            , {field: 'LoopGj', title: __('Cyclic advanced'), operate: false}
                            , {field: 'Rs', title: __('Day race'), operate: false}
                            , {field: 'Zs', title: __('Week race'), operate: false}
                            , {field: 'Ys', title: __('Month race'), operate: false}
                            , {field: 'Private', title: __('Private'), operate: false}
                            , {field: 'Club', title: __('Club'), operate: false}
                            ,
                            // {
                            //     field: 'operate',
                            //     title: __('Operate'),
                            //     width:73,
                            //     table: table,
                            //     events: Table.api.events.operate,
                            //     formatter: Controller.api.formatter.operate//改变编辑图标样式
                            // }
                        ]
                    ],

                });
                // 为表格1绑定事件
                Table.api.bindevent(table);
            },
            CattleCattle: function () {
                // 表格2
                var table2 = $("#table2");
                table2.bootstrapTable({
                    url: 'ql/base_search/playwarnum?gameId=32',
                    pk:'_id',
                    sortName: '_id',
                    search: false,
                    clickToSelect: true, //是否启用点击选中
                    dblClickToEdit: true,
                    extend: {
                        url: 'ql/base_search/playwarnum',
                        // close_url: 'general/crontab/log_check',
                        // table: 'crontab',
                    },
                    toolbar: '#toolbar2',
                    columns: [
                        [
                            {
                                field: 'AddTime',
                                title: __('Start time'),
                                formatter: Table.api.formatter.datetime,
                                operate: 'RANGE',
                                addclass: 'datetimerange',
                                visible: false
                            },
                            {field: 'PID', title:__('Id')}
                            , {field: 'NickName', title:__('Nick'),operate: false,}
                            , {field: 'PrimaryGold', title: __('Novice'), operate: false}
                            , {field: 'IntermediateGold', title: __('Ordinary'), operate: false}
                            , {field: 'AdvancedGold', title: __('Killer'), operate: false}
                            , {field: 'PrimaryLz', title: __('Chivalrous man'), operate: false}
                            , {field: 'IntermediateLz', title: __('Lonely defeat'), operate: false}
                            , {field: 'SeniorLz', title: __('Novice a rogue'), operate: false}
                            , {field: 'RoutineFree', title: __('Ordinary a rogue'), operate: false}
                            , {field: 'RoutineZj', title: __('Killer a rogue'), operate: false}
                            , {field: 'RoutineGj', title: __('Chivalrous man a rogue'), operate: false}
                            , {field: 'LoopFree', title: __('Lonely defeat a rogue'), operate: false}
                            , {field: 'LoopZj', title: __('Intermediate cycle'), operate: false}
                        ]
                    ],

                });
                // 为表格2绑定事件
                Table.api.bindevent(table2);
            },
            FriedGoldenFlower: function () {
                // 表格2
                var table3 = $("#table3");
                table3.bootstrapTable({
                    url: 'ql/base_search/playwarnum?gameId=33',
                    pk:'_id',
                    sortName: '_id',
                    search: false,
                    clickToSelect: true, //是否启用点击选中
                    dblClickToEdit: true,
                    extend: {
                        url: 'ql/base_search/playwarnum',
                        // edit_url: 'general/crontab/log_check',
                        // table: 'crontab',
                    },
                    toolbar: '#toolbar3',
                    columns: [
                        [
                            {
                                field: 'AddTime',
                                title: __('Start time'),
                                formatter: Table.api.formatter.datetime,
                                operate: 'RANGE',
                                addclass: 'datetimerange',
                                visible: false
                            },
                            {field: 'PID', title:__('Id')}
                            , {field: 'NickName', title:__('Nick'),operate: false,}
                            , {field: 'PrimaryGold', title: __('Flat field'), operate: false}
                            , {field: 'IntermediateGold', title: __('Boss field'), operate: false}
                            , {field: 'AdvancedGold', title: __('Aristocratic field'), operate: false}
                            , {field: 'PrimaryLz', title: __('Small capital'), operate: false}
                            , {field: 'IntermediateLz', title: __('Tyrant field'), operate: false}
                            , {field: 'SeniorLz', title: __('Royal field'), operate: false}
                            , {field: 'RoutineFree', title: __('Private field'), operate: false}
                        ]
                    ],

                });
                // 为表格2绑定事件
                Table.api.bindevent(table3);
            }
        },
        robotgold: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ql/base_search/robotgold'
                }
            });
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'c_t_time', title: '金币消耗量', operate: false}
                        , {field: 'c_t_time1', title: '时间', operate:'RANGE', addclass:'datetimerange',vizible:false}
                        , {field: 'c_t_channel_key', title: '金贝获得量',operate: false}
                        , {field: 'c_t_pay_user_num', title: '日期', operate: false}
                        , {field: 'c_t_pay_num', title: '全服金贝产出量', operate: false}
                        , {field: 'c_t_pay_amount', title: '全服金币产出量', operate: false}
                        , {field: 'c_t_new_user_num', title: '金币消耗占全服百分比', operate: false}
                        , {field: 'c_t_online_user_num', title: '金币获得占全服百分比', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageSize: 10
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                operate: function (value, row, index) {
                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);
                    if (options.extend.close_url !== '') {
                        buttons.push({
                            name: 'close',
                            icon: 'fa fa-user-times',
                            title: __('Close'),
                            extend: 'data-toggle="tooltip"',
                            classname: 'btn btn-xs btn-info btn-danger',
                            url: options.extend.close_url
                        });
                    }
                    if (options.extend.create_url !== '') {
                        buttons.push({
                            name: 'create',
                            icon: 'fa fa-plus',
                            title: __('Create club'),
                            extend: 'data-toggle="tooltip"',
                            classname: 'btn btn-xs btn-success btn-editone',
                            url: options.extend.create_url
                        });
                    }
                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                }

            }

        }

    };

    return Controller;
});