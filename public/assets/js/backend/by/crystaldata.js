define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template', 'table', 'form'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        tradelist: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'by/crystal_data/tradelist'
                }
            });

            var table = $("#table");
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "请输入ID|昵称";};

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'AddTime', title: '交易时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                        , {field: 'PlatForm', title: '渠道', searchList: $.getJSON("ajax/getPlatform"), formatter: Table.api.formatter.search}
                        , {field: 'PID', title: 'ID', formatter: Table.api.formatter.search}
                        , {field: 'NickName', title: '昵称', operate: 'LIKE', formatter: Table.api.formatter.search}
                        , {field: 'CrystalNum', title: '获得/消耗数量', operate: false, formatter:function (value, row, index) {
                            if(row.CrystalActionType == 1){
                                return "+"+value;
                            }else{
                                return "-"+value;
                            }
                        }}
                        , {field: 'Crysta', title: '操作后水晶数量', operate: false}
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
                    index_url: 'by/crystal_data/tradecount'
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
                        , {field: 'CrystalWhaleOutput', title: '鲸口夺宝产出水晶数量', operate: false}
                        , {field: 'CrystalHunterOutput', title: '水晶狩猎产出水晶数量', operate: false}
                        , {field: 'CrystalShopOutput', title: '水晶商城消耗水晶数量', operate: false}
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