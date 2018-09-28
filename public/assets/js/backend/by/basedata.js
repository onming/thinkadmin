define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template', 'table', 'form'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        summary: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/base_data/summary'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: '', operate: false}
                        , {field: 'ActiveUserNum', title: '活跃玩家', operate: false}
                        , {field: 'NewRegister', title: '新增玩家', operate: false}
                        , {field: 'PayAmount', title: '付费总额', operate: false}
                        , {field: 'PayUserNum', title: '付费人数', operate: false}
                        , {field: 'PayNum', title: '付费次数', operate: false}
                        , {field: 'averageOnline', title: '平均在线时长(min)', operate: false}
                        , {field: 'NewUserAvgOnline', title: '新增用户平均在线时长(min)', operate: false}
                        , {field: 'NewUserArpu', title: '新增用户ARPU', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: false,
                pageList: [2, 5, 10],
                pageSize: 2
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
        realtime: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            var chartData = {
                c_t_time: [],
                c_t_online_user_num: [],
                c_t_pay_amount: [],
                c_t_lottery_output: [],
                c_t_packet_output: [],
            };

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['在线人数','今日充值','奖券产出','红包产出']
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.c_t_time
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: '在线人数',
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: chartData.c_t_online_user_num
                }, {
                        name: '今日充值',
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {}
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: chartData.c_t_pay_amount
                 }, {
                    name: '奖券产出',
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: chartData.c_t_lottery_output
                }, {
                    name: '红包产出',
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: chartData.c_t_packet_output
                }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            $(window).resize(function () {
                myChart.resize();
            });

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/base_data/realtime'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'c_t_time', title: '时间', operate:'RANGE', addclass:'datetimerange'}
                        , {field: 'c_t_channel_key', title: '渠道',visible: false,searchList: $.getJSON("ajax/getPlatform")}
                        , {field: 'c_t_pay_user_num', title: '充值人数', operate: false}
                        , {field: 'c_t_pay_num', title: '充值次数', operate: false}
                        , {field: 'c_t_pay_amount', title: '充值总额', operate: false}
                        , {field: 'c_t_new_user_num', title: '新增人数', operate: false}
                        , {field: 'c_t_online_user_num', title: '在线人数', operate: false}
                        , {field: 'c_t_room_user_num', title: '房间人数', operate: false}
                        , {field: 'c_t_lottery_output', title: '奖券产出', operate: false}
                        , {field: 'c_t_lottery_consume', title: '奖券消耗', operate: false}
                        , {field: 'c_t_prop_output', title: '道具产出', operate: false}
                        , {field: 'c_t_prop_use', title: '道具使用', operate: false}
                        , {field: 'c_t_packet_output', title: '红包产出', operate: false}
                        , {field: 'c_t_packet_exchange', title: '红包兑换', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageSize: 10
            });

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                $("#pcu").text(data.extend.pcu);
                $("#acu").text(data.extend.acu);

                myChart.setOption({
                    xAxis: {
                        data: data.extend.echarts_data.c_t_time
                    },
                    series: [
                        {name: '在线人数',data: data.extend.echarts_data.c_t_online_user_num}
                        ,{name: '今日充值',data: data.extend.echarts_data.c_t_pay_amount}
                        ,{name: '奖券产出',data: data.extend.echarts_data.c_t_lottery_output}
                        ,{name: '红包产出',data: data.extend.echarts_data.c_t_packet_output}
                    ]
                });
                myChart.resize();
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
        payrank: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/base_data/payrank'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'ranking', title: '排名', formatter:function (value, row, index) {
                                return index+1;
                            }}
                        , {field: 'userId', title: '用户ID'}
                        , {field: 'playerName', title: '昵称'}
                        , {field: 'amount', title: '总充值金额'}
                        , {field: 'firstTime', title: '第一次充值时间'}
                        , {field: 'lastTime', title: '最后一次充值时间'}
                        , {field: 'payNum', title: '付费次数'}
                        , {field: 'onlineHour', title: '在线小时'}
                        , {field: 'channelKey', title: '渠道'}
                    ]
                ],
                striped: true,
                search: false,
                pagination: false,
                pageSize: 100
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
        stockvalue: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/base_data/stockvalue'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: '时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'GoldStock', title: '金币总存量', operate: false}
                        , {field: 'ActiveGoldStock', title: '活跃金币存量', operate: false}
                        , {field: 'PropStock', title: '道具总存量', operate: false}
                        , {field: 'ActivePropStock', title: '活跃道具存量', operate: false}
                        , {field: 'NewGiftGoldStock', title: '新增赠送金币', operate: false}
                        , {field: 'GoldStockDiff', title: '存量日差值', operate: false}
                        , {field: 'PayAmount', title: '充值总额', operate: false}
                        , {field: 'RedBagAmount', title: '红包总额', operate: false}
                        , {field: 'LotteryAmount', title: '奖券总额', operate: false}
                        , {field: 'PlatForm', title: '渠道标识',visible: false,searchList: $.getJSON("ajax/getPlatform")}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageSize: 10
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
        }
    };

    return Controller;
});