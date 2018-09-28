define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Table, Form, Echarts, undefined, Template) {

    var Controller = {
        current: function() {
            //图表渲染
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            var chartData = {
                xAxis_time: [],
                prop_sale: [],
                prop_give: [],
                prop_produce: []
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
                    data: [__('Prop sale'), __('Prop give'), __('Prop produce')]
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
                    data: chartData.xAxis_time
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [
                    {
                        name: __('Prop sale'),
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
                        data: chartData.prop_sale
                    },
                    {
                        name: __('Prop give'),
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
                        data: chartData.prop_give
                    },
                    {
                        name: __('Prop produce'),
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
                        data: chartData.prop_produce
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
                    index_url: 'by/prop_data/current',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: __('Time'), operate:'RANGE', addclass:'datetimerange'}
                        , {field: 'PlatForm', title: __('Channel'),visible: false,searchList: $.getJSON("ajax/getPlatform")}
                        , {field: 'PropSaled', title: __('Prop saled'), operate: false}
                        , {field: 'PropGift', title: __('Prop gift'), operate: false}
                        , {field: 'PropOutput', title: __('Prop output'), operate: false}
                        , {field: 'RedBagOutput', title: __('RedBag output'), operate: false}
                        , {field: 'CrystalOutput', title: __('Crystal output'), operate: false}
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
                    data.extend.echarts_data = {
                        Time: [],
                        PropSaled: [],
                        PropGift: [],
                        PropOutput: [],
                    }

                    var $li="";
                    $li = "<div id='no-data'><span class='b threefont' style='letter-spacing:5px;position: absolute;margin-left: 47.7%;top: 40%;color:#ddd'>暂无数据</span></div>";
                    $('#echart').append($li);
                }else{
                    $('#no-data').remove();
                }

                myChart.setOption({
                    xAxis: {
                        data: data.extend.echarts_data.Time
                    },
                    series: [
                        {name: __('Prop sale'),data: data.extend.echarts_data.PropSaled}
                        ,{name: __('Prop give'),data: data.extend.echarts_data.PropGift}
                        ,{name: __('Prop produce'),data: data.extend.echarts_data.PropOutput}
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
        detail: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/prop_data/detail',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'AddTime', title: __('AddTime'), operate:'RANGE', addclass:'datetimerange'}
                        , {field: 'GivePID', title: __('GivePID')}
                        , {field: 'GiveNickName', title: __('GiveNickName')}
                        , {field: 'Amount', title: __('Amount'), operate: false}
                        , {field: 'PropNum', title: __('PropNum'), operate: false}
                        , {field: 'GivedNum', title: __('GivedNum'), operate: false}
                        , {field: 'PID', title: __('PID'), operate: false}
                        , {field: 'NickName', title: __('NickName'), operate: false}
                    ]
                ],
                striped: false,
                pagination: true,
                pageSize: 10,
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