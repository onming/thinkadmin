define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template', 'table', 'form'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {
    var Controller = {
        remain:function(){
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
                var chartData = {
                    Time: [],
                    CrKeep: [],
                    SrKeep: [],
                    QrKeep: [],
                    SWrKeep: [],
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
                    data: ['次日留存', '三日留存', '七日留存', '十五日留存']
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
                    data: chartData.Time
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: '次日留存',
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
                    data: chartData.CrKeep
                }, {
                    name: '三日留存',
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
                    data: chartData.SrKeep
                }, {
                    name: '七日留存',
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
                    data: chartData.QrKeep
                }, {
                    name: '十五日留存',
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
                    data: chartData.SWrKeep
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
                    index_url: 'by/operate_data/remain'
                }
            });
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: '时间',operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'NewPlayerNum', title: '新增人数', operate: false}
                        , {field: 'ActivePlayerNuam', title: '活跃人数', operate: false}
                        , {field: 'PayMoney', title: '充值总额', operate: false}
                        , {field: 'CrKeep', title: '次日留存', operate: false}
                        , {field: 'SrKeep', title: '三日留存', operate: false}
                        , {field: 'QrKeep', title: '七日留存', operate: false}
                        , {field: 'SWrKeep', title: '十五日留存', operate: false}
                        , {field: 'PlatForm', title: '渠道', visible: false,searchList: $.getJSON("ajax/getPlatform"), formatter: Table.api.formatter.search}
                    ]
                ],

            });

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                if(data.echarts_data.Time){
                    myChart.setOption({
                        xAxis: {
                            data: data.echarts_data.Time
                        },
                        series: [
                            {name: '次日留存',data: data.echarts_data.CrKeep}
                            ,{name: '三日留存',data: data.echarts_data.SrKeep}
                            ,{name: '七日留存',data: data.echarts_data.QrKeep}
                            ,{name: '十五日留存',data: data.echarts_data.SWrKeep}
                        ]
                    });
                }
                myChart.resize();
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        channel: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/operate_data/channel'
                }
            });
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: '时间',operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'NewPlayerNum', title: '新增人数', operate: false}
                        , {field: 'ActivePlayerNuam', title: '活跃人数', operate: false}
                        , {field: 'PayMoney', title: '当日充值', operate: false}
                        , {field: 'NewPayMoney', title: '新增付费', operate: false}
                        , {field: 'NewPayPlayerNum', title: '新增付费人数', operate: false}
                        , {field: 'NewPayRate', title: '新增付费率', operate: false}
                        , {field: 'PayPlayerNuam', title: '付费用户数', operate: false}
                        , {field: 'ActivePayRate', title: '活跃付费率', operate: false}
                        , {field: 'ARPU', title: 'arpu', operate: false}
                        , {field: 'ARPPU', title: 'arppu', operate: false}
                        , {field: 'PlatForm', title: '渠道', visible: false,searchList: $.getJSON("ajax/getPlatform"), formatter: Table.api.formatter.search}
                    ]
                ],

            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        payment: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            var chartData = {
                PayInterval: [],
                PayPlayerNum: []
            };
            // 指定图表的配置项和数据
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:['人数']
                },
                calculable : true,
                xAxis : [
                    {
                        type : 'value',
                        boundaryGap : [0, 0.01]
                    }
                ],
                yAxis : [
                    {
                        type : 'category',
                        data : chartData.PayInterval
                    }
                ],
                series : [
                    {
                        name:'人数',
                        type:'bar',
                        data: chartData.PayPlayerNum
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
                    index_url: 'by/operate_data/payment'
                }
            });
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'PayInterval', title: '付费区间', operate: false}
                        , {field: 'PayPlayerNum', title: '付费人数', operate: false}
                        , {field: 'PayCount', title: '付费次数', operate: false}
                        , {field: 'Amout', title: '付费总额', operate: false}
                        , {field: 'PayHold', title: '占比', operate: false}
                        , {field: 'Time', title: '时间',visible: false,operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'PlatForm', title: '渠道', visible: false,searchList: $.getJSON("ajax/getPlatform"), formatter: Table.api.formatter.search}
                    ]
                ],
            });
            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                console.log(data);
                if(data.echarts_data){
                    myChart.setOption({
                        yAxis: {
                            data: data.echarts_data.PayInterval
                        },
                        series: [
                            {name: '人数',data: data.echarts_data.PayPlayerNum}
                        ]
                    });
                }
                myChart.resize();
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        }
    };
    return Controller;
});