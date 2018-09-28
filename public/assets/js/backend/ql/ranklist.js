define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($,undefined,Backend,Table, Form) {

    var Controller = {
        profit: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ql/rank_list/profit'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'ranking', title: '排名', operate: false}
                        , {field: 'pid', title: '用户PID', operate: false}
                        , {field: 'nickname', title: '昵称', operate: false}
                        , {field: 'shell', title: '金贝', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageList: [20, 30, 50],
                pageSize: 20
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
        wealth: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ql/rank_list/wealth'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'ranking', title: '排名', operate: false}
                        , {field: 'pid', title: '用户PID', operate: false}
                        , {field: 'nickname', title: '昵称', operate: false}
                        , {field: 'gold', title: '金币', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageList: [20, 30, 50],
                pageSize: 20
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
        diamond: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'ql/rank_list/diamond'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'ranking', title: '排名', operate: false}
                        , {field: 'pid', title: '用户PID', operate: false}
                        , {field: 'nickname', title: '昵称', operate: false}
                        , {field: 'diamond', title: '钻石', operate: false}
                    ]
                ],
                striped: true,
                search: false,
                pagination: true,
                pageList: [20, 30, 50],
                pageSize: 20
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