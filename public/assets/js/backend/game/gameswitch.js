define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'game/game_switch/index',
                    add_url: 'game/game_switch/add',
                    edit_url: 'game/game_switch/edit',
                    del_url: 'game/game_switch/del',
                    multi_url: 'game/game_switch/multi',
                    dragsort_url: 'ajax/weigh',
                    table: 'game_switch',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                searchFormVisible: false,
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'scene.scene_name', title: __('Scene_name')},
                        {field: 'channel.channel_name', title: __('Channel_name')},
                        {field: 'vip_level', title: __('Vip_level')},
                        {field: 'timespace', title: __('Timespace')},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'status', title: __('Switch'), formatter: Controller.api.formatter.toggle},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams: function (params) {
                    params.filter = JSON.stringify({channel_id:Config.init_channle_id});
                    return params;
                },
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

            //显示隐藏渠道列表
            $(document).on("click", "a.btn-channel", function () {
                $("#scenepanel").toggleClass("col-md-9", $("#channelbar").hasClass("hidden"));
                $("#channelbar").toggleClass("hidden");
            });

            //渠道树形列表
            require(['jstree'], function () {
                $('#channeltree').jstree({
                    "themes": {
                        "stripes": true
                    },
                    "types": {
                        'default': {
                            'icon': 'fa fa-list'  // 删除默认图标 false
                        },
                    },
                    'plugins': ["types", "themes"],
                    "core": {
                        'check_callback': true,
                        "data": Config.channelList
                    }
                }).on('ready.jstree', function (e, data) {
                    //选中初始节点
                    $('#channeltree').jstree('select_node', Config.init_channle_id, true);
                });

                //展开
                $(document).on("click", "#expandall", function () {
                    $("#channeltree").jstree($(this).prop("checked") ? "open_all" : "close_all");
                });

                $('#channeltree').on("changed.jstree", function (e, data) {
                    // console.log(data);
                    // console.log(data.selected);
                    var options = table.bootstrapTable('getOptions');
                    options.pageNumber = 1;
                    options.queryParams = function (params) {
                        params.filter = JSON.stringify(data.selected.length > 0 ? {channel_id: data.selected.join(",")} : {});
                        params.op = JSON.stringify(data.selected.length > 0 ? {channel_id: 'in'} : {});
                        return params;
                    };
                    table.bootstrapTable('refresh', {});
                    return false;
                });
            });
            
            //一键同步生成开关
            $(document).on('click', '.btn-sync', function () {
                Layer.confirm(__('Are you sure you want to sync?'),{icon:3, title:'温馨提示', offset: '30%'},function(index){
                    var index = Layer.load(0, {offset: '40%'});
                    setTimeout(function(){
                        $.ajax({
                            url: 'game/game_switch/sync',
                            type: 'get',
                            dataType: 'json',
                            success: function(ret){
                                Layer.closeAll();
                                Toastr.success(ret.msg);
                                table.bootstrapTable('refresh', {url:'game/game_switch/index'});
                            },
                            error: function (ret) {
                                Layer.close(index);
                                Toastr.error(ret.msg);
                            }
                        });
                    },1000);
                })
            })
            //重置数据
            $(document).on('click', '.btn-reset', function () {
                Layer.confirm(__('Are you sure you want to reset server?'),{icon:3, title:'温馨提示', offset: '30%'},function(index){
                    var index = Layer.load(0, {offset: '40%'});
                    setTimeout(function(){
                        $.ajax({
                            type: 'get',
                            url: 'game/game_switch/reset',
                            dataType: 'json',
                            success: function (ret) {
                                Layer.closeAll();
                                if(ret.code == 1){
                                    Toastr.success(ret.msg);
                                }else{
                                    Toastr.error(ret.msg);
                                }
                            },
                            error: function (ret) {
                                Layer.close(index);
                                Toastr.error(ret.msg)
                            }
                        });
                    },1000);
                })
            })

        },
        add: function () {
            Controller.api.bindevent();
            Controller.api.common();
        },
        edit: function () {
            Controller.api.bindevent();
            Controller.api.common();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                toggle: function (value, row, index) {
                    value = parseInt(value);
                    var color = typeof this.color !== 'undefined' ? this.color : 'success';
                    var yes = typeof this.yes !== 'undefined' ? this.yes : 1;
                    var no = typeof this.no !== 'undefined' ? this.no : 0;
                    return "<a href='javascript:;' data-toggle='tooltip' title='" + __('Click to toggle') + "' class='btn-change' data-id='"
                        + row.id + "' data-params='" + this.field + "=" + (value ? no : yes) + "'><i class='fa fa-toggle-on " + (value == yes ? 'text-' + color : 'fa-flip-horizontal text-gray') + " fa-2x'></i></a>";
                }
            },
            rendertimepicker: function (form) {
                if ($(".datetimepicker", form).size() > 0) {
                    require(['bootstrap-datetimepicker'], function () {
                        var options = {
                            format: 'YYYY-MM-DD HH:mm:ss',
                            icons: {
                                time: 'fa fa-clock-o',
                                date: 'fa fa-calendar',
                                up: 'fa fa-chevron-up',
                                down: 'fa fa-chevron-down',
                                previous: 'fa fa-chevron-left',
                                next: 'fa fa-chevron-right',
                                today: 'fa fa-history',
                                clear: 'fa fa-trash',
                                close: 'fa fa-remove'
                            },
                            showTodayButton: true,
                            showClose: true
                        };
                        $('.datetimepicker', form).parent().css('position', 'relative');
                        $('.datetimepicker', form).datetimepicker(options);
                    });
                }
            },
            common: function () {
                //渠道和场景联动，单向
                $(document).on('change', '#c-channel_id', function () {
                    var channel_id = $(this).val();
                    if(channel_id == '' || channel_id == 'undefined'){
                        Toastr.warning(__('Empty channel id'));
                        return;
                    }
                    $.ajax({
                        url: 'game/game_switch/ajaxGetScene',
                        type: 'get',
                        data: {channel_id: channel_id},
                        dataType: 'json',
                        success: function(ret){
                            if(ret.code == 1){
                                var html = '';
                                $.each(ret.data, function (index, value) {
                                    html += '<option value="'+value.id+'"';
                                    if(value.disabled){
                                        html += ' disabled';
                                    }
                                    html += '>' + value.scene_name + '</option>';
                                });
                                $('#c-scene_id').html(html);
                                $('#c-scene_id').selectpicker('refresh');
                            }else{
                                Toastr.error(ret.msg)
                            }
                        },
                        error: function (ret) {
                            Toastr.error(ret.msg);
                        }
                    });
                });

                //追加时间
                $(document).on('click', '.btn-append', function () {
                    var dd = $('dd:eq(1)', '.timeramge');
                    //dd.clone().insertAfter(dd); //克隆行下增加一行
                    var new_dd = dd.clone();
                    var size = $('.timespace').size();
                    new_dd.find('input:eq(0)').attr('name', 'row[timespace]['+ size +'][start_time]');
                    new_dd.find('input:eq(1)').attr('name', 'row[timespace]['+ size +'][end_time]');
                    if(size <= 0){
                        var initdate = $(this).attr('data-initdate');
                        new_dd = '<dd class="form-inline timespace">\n' +
                            '<input class="form-control datetimepicker form-control" data-date-format="HH:mm:ss" data-use-current="true" name="row[timespace][0][start_time]" type="text" value="' + initdate + '">\n' +
                            '<input class="form-control datetimepicker form-control" data-date-format="HH:mm:ss" data-use-current="true" name="row[timespace][0][end_time]" type="text" value="' + initdate + '">\n' +
                            '<span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>\n' +
                            '</dd>';
                    }
                    $(this).closest('dd').before(new_dd);
                    Controller.api.rendertimepicker($('.timeramge'))
                });

                //移除时间
                $(document).on('click', '.btn-remove', function () {
                    $(this).closest('dd').remove();
                });
            }
        }
    };
    return Controller;
});