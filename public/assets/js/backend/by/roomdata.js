define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Table, Form, Echarts, undefined, Template) {

    var Controller = {
        current: function() {
            //图表渲染
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            var chartData = {
                xAxis: [],
                gold_profit: [],
                online_nums: [],
                avg_profit: []
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
                    top: 'top',
                    data: [__('Gold profit'), __('Online nums'), __('Avg profit')]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true},
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.xAxis
                },
                yAxis: [{type: 'value'}],
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [
                    {
                        name: __('Gold profit'),
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
                        data: chartData.gold_profit
                    },
                    {
                        name: __('Online nums'),
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
                        data: chartData.online_nums
                    },
                    {
                        name: __('Avg profit'),
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
                        data: chartData.avg_profit
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            $(window).resize(function () {
                myChart.resize();
            });

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/room_data/current',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: __('Time'), visible: false, operate:'RANGE', addclass:'datetimerange'}   //搜索用的
                        , {field: 'PlatForm', title: __('PlatForm'),visible: false,searchList: $.getJSON("ajax/getPlatform")}   //搜索用的
                        , {field: 'RoomType', title: __('RoomType'), operate: false}
                        , {field: 'GoldValue', title: __('GoldValue'), operate: false}
                        , {field: 'IsOnline', title: __('IsOnline'), operate: false}
                        , {field: 'PlayerNum', title: __('PlayerNum'), operate: false}
                        , {field: 'InRoomNum', title: __('InRoomNum'), operate: false}
                        , {field: 'PropValue', title: __('PropValue'), operate: false}
                        , {field: 'totalProfitLoss', title: __('totalProfitLoss'), operate: false}
                        , {field: 'TicketValue', title: __('TicketValue'), operate: false}
                        , {field: 'RedBagValue', title: __('RedBagValue'), operate: false}
                        , {field: 'newPlayerProfitLoss', title: __('newPlayerProfitLoss'), operate: false}
                        , {field: 'TotalOnline', title: __('TotalOnline'), operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageSize: 10
            });

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //$("#pcu").text(data.extend.pcu);
                //$("#acu").text(data.extend.acu);
                if(JSON.stringify(data.extend.echarts_data) == "{}"){
                    data.extend.echarts_data = {  //服务器返回的参数
                        RoomType: [],
                        GoldValue: [],
                        IsOnline: [],
                        totalProfitLoss: [],
                    }

                    var $li="";
                    $li = "<div id='no-data'><span class='b threefont' style='letter-spacing:5px;position: absolute;margin-left: 47.7%;top: 40%;color:#ddd'>暂无数据</span></div>";
                    $('#echart').append($li);
                }else{
                    $('#no-data').remove();
                }

                myChart.setOption({
                    xAxis: {
                        data: data.extend.echarts_data.RoomType
                    },
                    series: [
                        {name: __('Gold profit'), data: data.extend.echarts_data.GoldValue}
                        ,{name: __('Online nums'), data: data.extend.echarts_data.IsOnline}
                        ,{name: __('Avg profit'), data: data.extend.echarts_data.totalProfitLoss}
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
            Controller.api.bindevent();

        },
        arena: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/room_data/arena',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [

                        {field: 'Time', title: __('Time'), visible: false, operate:'RANGE', addclass:'datetimerange'}   //搜索用的
                        , {field: 'PlatForm', title: __('PlatForm'),visible: false,searchList: $.getJSON("ajax/getPlatform")}   //搜索用的
                        , {field: 'ArenaType', title: __('ArenaType'), operate: false}
                        , {field: 'ArenaEntryFeeValue', title: __('ArenaEntryFeeValue'), operate: false}
                        , {field: 'ActivePlayerNum', title: __('ActivePlayerNum'), operate: false}
                        , {field: 'ArenaoutRewardValue', title: __('ArenaoutRewardValue'), operate: false}
                        , {field: 'ArenaNum', title: __('ArenaNum'), operate: false}
                        , {field: 'ProfitLoss', title: __('ProfitLoss'), operate: false}
                    ]
                ],
                striped: false,
                pagination: true,
                pageSize: 10,
                search: false,
            });

            //表格渲染完成后
            table.on('load-success.bs.table', function (e, data) {
                $('#total_enroll').html(data.extend.total_enroll);
                $('#active_nums').html(data.extend.active_nums);
                $('#total_profix').html(data.extend.total_profix);
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
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
        }
    };
    return Controller;
});