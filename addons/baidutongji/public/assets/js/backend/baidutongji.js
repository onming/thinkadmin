define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme', , '../../addons/baidutongji/js/china'], function ($, undefined, Backend, Table, Form, Echarts, undefined, undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'baidutongji/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'site_id',
                // pk: 'id',
                columns: [
                    [
                        {field: 'state', checkbox: true,},
                        {field: 'site_id', title: 'ID'},
                        {field: 'domain', title: __('Title'),},
                        {field: 'status', title: __("Status"), formatter: Table.api.formatter.status},
                        {field: 'create_time', title: __('Create Time'), addclass: 'datetimerange'},
                        {
                            field: 'operate', title: __('Operate'), table: table,
                            // events: Table.api.events.operate,
                            buttons: [{
                                name: 'addtabs',
                                text: '网站详情',
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-addtabs',
                                url: 'baidutongji/detail'
                            }],
                            formatter: Table.api.formatter.operate
                        }

                    ]
                ],
                search: false,
                commonSearch: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 清除缓存
            $(".btn-clearcache").data("success", function(data){
               table.bootstrapTable('refresh');
            });
        },
        detail: function () {
            var api = Config.api;
            var siteid = Config.siteid;
            // 趋势图
            var myChart = Echarts.init(document.getElementById('trend'));
            myChart.showLoading();
            var option = {
                title: {
                    // text: '趋势图'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#6a7985'
                        }
                    }
                },
                legend: {
                    data: []
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: []
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: []
            };
            myChart.setOption(option);

            $.get(api, {'random': Math.random(), 'ids': siteid, 'step': 'trend'}).done(function (result) {
                myChart.hideLoading();
                // 填入数据
                myChart.setOption({
                    legend: {
                        data: result.legend
                    },
                    xAxis: {
                        data: result.xaxis
                    },
                    series: result.series
                });
            });
            // 来源
            var inbie = Echarts.init(document.getElementById('inbie'));
            inbie.showLoading();
            var option = {
                title: {
                    text: '用户访问来源',
                    subtext: '数据来自于百度',
                    x: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: []
                },
                series: [
                    {
                        name: '访问来源',
                        type: 'pie',
                        radius: '55%',
                        center: ['50%', '60%'],
                        data: [],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };
            inbie.setOption(option);
            $.get(api, {'random': Math.random(), 'ids': siteid, 'step': 'inbie'}).done(function (result) {
                inbie.hideLoading();
                // 填入数据
                inbie.setOption({
                    legend: {
                        data: result.legend
                    },
                    series: [
                        {
                            data: result.series
                        }
                    ]
                });
            });

            /** TOP10 */
            function Top10(api, param, box) {
                this.api = api;
                this.param = param;
                this.loadData = function (params) {
                    $.get(this.api, this.param, function (result) {
                        if (result) {
                            var html = '<tbody>';
                            for (var index = 0; index < result.length - 10; index++) {
                                html += '<tr>';
                                // html += '<th scope="row">' + (index + 1) + '</th>';
                                html += '<td title="' + result[index]['name'] + '"><a target="_blank" class="ellipsis" href="' + result[index]['name'] + '">' + result[index]['name'].substr(0, 80) + '</a></td>';
                                html += '<td>' + result[index]['count'] + '</td>';
                                html += '<td>' + result[index]['perc'] + '</td>';
                            }
                            html += '</tr>';
                            html += '</tbody>';
                            $(box).nextAll().remove().end().after(html);
                        }
                    })
                }
            }

            // Top10入口页面
            var param = {'random': Math.random(), 'ids': siteid, 'step': 'enter'};
            var top = new Top10(api, param, '#enter-page thead');
            var res = top.loadData();

            // Top10受访问页面
            var param = {'random': Math.random(), 'ids': siteid, 'step': 'access'};
            var top = new Top10(api, param, '#access-page thead');
            var res = top.loadData();


            // 区域分布
            var chinaMap = Echarts.init(document.getElementById('chinaMap'));
            var option = {
                title: {
                    text: '浏览量地域分布',
                    subtext: '数据来自于百度',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: ['浏览量']
                },
                visualMap: {
                    min: 0,
                    max: 0,
                    left: 'left',
                    top: 'bottom',
                    text: ['高', '低'],
                    calculable: true
                },
                toolbox: {
                    show: true,
                    orient: 'vertical',
                    left: 'right',
                    top: 'center',
                    feature: {
                        dataView: {readOnly: false},
                        restore: {},
                        saveAsImage: {}
                    }
                },
                series: [
                    {
                        name: '浏览量',
                        type: 'map',
                        mapType: 'china',
                        label: {
                            normal: {
                                // show: true
                            },
                            emphasis: {
                                show: true
                            }
                        },
                        data: []
                    }
                ]
            };
            chinaMap.setOption(option);
            chinaMap.showLoading();
            $.get(api, {'random': Math.random(), 'ids': siteid, 'step': 'chinamap'}).done(function (result) {
                chinaMap.hideLoading();
                chinaMap.setOption({
                    visualMap: {
                        max: result.max
                    },
                    series: [
                        {
                            data: result.citymap
                        }
                    ]
                });
            });

            /** 选择导航时间 */
            $('#toolbar a').click(function () {
                $(this).addClass('disabled').siblings().removeClass('disabled');
                // 请求趋势图API
                var _this = $(this);
                var memo = _this.attr('memo');
                var gran = _this.attr('gran');
                var stimestamp = Math.floor(Date.parse(new Date()) / 1000) + (parseInt(memo) * 1 * 24 * 3600);

                myChart.showLoading();
                $.get(api, {
                    'random': Math.random(),
                    'ids': siteid,
                    'step': 'trend',
                    'stime': stimestamp,
                    'gran': gran
                }, function (result) {
                    myChart.hideLoading();
                    myChart.setOption({
                        legend: {
                            data: result.legend
                        },
                        xAxis: {
                            data: result.xaxis
                        },
                        series: result.series
                    });
                });

                // 请求TOP10API
                var api = api;
                var top = new Top10(api, {
                    'random': Math.random(),
                    'ids': siteid,
                    'step': 'enter',
                    'stime': stimestamp
                }, '#enter-page thead');
                top.loadData();
                var top = new Top10(api, {
                    'random': Math.random(),
                    'ids': siteid,
                    'step': 'access',
                    'stime': stimestamp
                }, '#access-page thead');
                top.loadData();

                // 饼图
                inbie.showLoading();
                $.get(api, {
                    'random': Math.random(),
                    'ids': siteid,
                    'step': 'inbie',
                    'stime': stimestamp
                }).done(function (result) {
                    inbie.hideLoading();
                    // 填入数据
                    inbie.setOption({
                        legend: {
                            data: result.legend
                        },
                        series: [
                            {
                                data: result.series
                            }
                        ]
                    });
                });

                // 请求地域API
                chinaMap.showLoading();
                $.get(api, {
                    'random': Math.random(),
                    'ids': siteid,
                    'step': 'chinamap',
                    'stime': stimestamp,
                    'gran': gran
                }).done(function (result) {
                    chinaMap.hideLoading();
                    chinaMap.setOption({
                        visualMap: {
                            max: result.max
                        },
                        series: [
                            {
                                data: result.citymap
                            }
                        ]
                    });
                });

            })
        },
    };
    return Controller;
});