define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template', 'table', 'form'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        tradelist: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/diamond_data/tradelist'
                }
            });

            var table = $("#table");
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "请输入ID|昵称";};

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'AddTime', title: '时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'PlatForm', title: '渠道', searchList: $.getJSON("ajax/getPlatform"), formatter: Table.api.formatter.search}
                        , {field: 'PID', title: 'ID', formatter: Table.api.formatter.search}
                        , {field: 'NickName', title: '昵称', operate: 'LIKE', formatter: Table.api.formatter.search}
                        , {field: 'DiamondNum', title: '获得/消耗数量', operate: false, formatter:function (value, row, index) {
                            if(row.DiamondActionType == 1){
                                return "+"+value;
                            }else{
                                return "-"+value;
                            }
                        }}
                        , {field: 'CurDiamond', title: '操作后钻石数量', operate: false}
                        , {field: 'Source', title: '获得/消耗途径', operate: false}
                        , {field: 'Describe', title: '获得/消耗目标', operate: false}
                    ]
                ],
                striped: true,
                search: true,
                pagination: true,
                pageList: [10, 20, 50],
                pageSize: 10
            });

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                $("#count").text(data.extend.count);
                $("#huode").text(data.extend.huode);
                $("#xiaohao").text(data.extend.xiaohao);
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
        tradecount: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/diamond_data/tradecount'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'Time', title: '时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'PlatForm', title: '渠道', searchList: $.getJSON("ajax/getPlatform"), formatter: Table.api.formatter.search}
                        , {field: 'Diamond_1', title: '钻石充值', operate: false}
                        , {field: 'Diamond_2', title: '充值奖励', operate: false}
                        , {field: 'Diamond_3', title: '商城消耗', operate: false}
                        , {field: 'Diamond_4', title: '维修炮台消耗', operate: false}
                        , {field: 'Diamond_5', title: 'PVE恢复体力消耗', operate: false}
                        , {field: 'Diamond_6', title: '鲸口夺宝消耗', operate: false}
                        , {field: 'Diamond_7', title: '解锁成长任务消耗', operate: false}
                        , {field: 'Diamond_8', title: '购买炮台消耗', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageList: [10, 20, 50],
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