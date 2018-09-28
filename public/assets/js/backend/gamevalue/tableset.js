define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'gamevalue/tableset/index',
                    add_url: 'gamevalue/tableset/add',
                    edit_url: 'gamevalue/tableset/edit',
                    del_url: 'gamevalue/tableset/del',
                    multi_url: 'gamevalue/tableset/table/multi',
                    table: 'tableset',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh desc,id asc',
                columns: [
                    [
                        {field: 'state', checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Data table name')},
                        {field: 'name', title: __('Data table English name')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh')},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            $("form.edit-form").data("validator-options", {
                display: function (elem) {
                    return $(elem).closest('tr').find("td:first").text();
                }
            });
        },
        add: function () {
            Form.config = {
                fieldlisttpl:'<dd class="form-inline">' +
                '<input type="text" name="rows[<%=index%>][name]" class="form-control" data-rule="required" size="15" /> ' +
                '<input type="text" name="rows[<%=index%>][title]" class="form-control" data-rule="required" size="15" /> ' +
                ' <select name="rows[<%=index%>][type]" class="form-control">\n' +
                '                <option value="int">整型(int)</option>\n' +
                '                <option value="float">浮点型(float)</option>\n' +
                '                <option value="char">字符型(char)</option>\n' +
                '                <option value="varchar">可变长字符(varchar)</option>\n' +
                '                <option value="date">date</option>\n' +
                '                <option value="datetime">datetime</option>\n' +
                '                <option value="text">text</option>\n' +
                '                </select> ' +
                '<span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span> ' +
                '<span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>' +
                '</dd>'

            };
            Form.api.bindevent($("form.edit-form"));
            Controller.api.bindevent();
        },
        edit: function () {
            Form.config = {
                fieldlisttpl:'<dd class="form-inline">' +
                '<input type="text" name="rows[<%=index%>][name]" class="form-control" data-rule="required" size="15" /> ' +
                '<input type="text" name="rows[<%=index%>][title]" class="form-control" data-rule="required" size="15" /> ' +
                ' <select name="rows[<%=index%>][type]" class="form-control">\n' +
                '                <option value="int">整型(int)</option>\n' +
                '                <option value="float">浮点型(float)</option>\n' +
                '                <option value="char">字符型(char)</option>\n' +
                '                <option value="varchar">可变长字符(varchar)</option>\n' +
                '                <option value="date">date</option>\n' +
                '                <option value="datetime">datetime</option>\n' +
                '                <option value="text">text</option>\n' +
                '                </select> ' +
                '<span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span> ' +
                '<span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>' +
                '</dd>'

            };
            Form.api.bindevent($("form.edit-form"));
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        },
    };




    var basic_form = $('#data-form');
    //绑定fieldlist
    if ($(".fieldlist2", basic_form).size() > 0) {
        require(['dragsort', 'template'], function (undefined, Template) {
            //刷新隐藏textarea的值
            var refresh = function (name) {
                var data = {};
                var textarea = $("textarea[name='" + name + "']", basic_form);
                var container = textarea.closest("dl");
                var template = container.data("template");
                $.each($("input,select", container).serializeArray(), function (i, j) {
                    var reg = /\[(\w+)\]\[(\w+)\]$/g;
                    var match = reg.exec(j.name);
                    if (!match)
                        return true;
                    match[1] = "x" + parseInt(match[1]);
                    if (typeof data[match[1]] == 'undefined') {
                        data[match[1]] = {};
                    }
                    data[match[1]][match[2]] = j.value;
                });
                var result = template ? [] : {};
                $.each(data, function (i, j) {
                    if (j) {
                        if (!template) {
                            if (j.key != '') {
                                result[j.key] = j.value;
                            }
                        } else {
                            result.push(j);
                        }
                    }
                });

                textarea.val(JSON.stringify(result));
            };
            //监听文本框改变事件
            $(document).on('change keyup', ".fieldlist2 input,.fieldlist2 textarea,.fieldlist2 select", function () {
                refresh($(this).closest("dl").data("name"));
            });
            //追加控制
            $(".fieldlist2", basic_form).on("click", ".btn-append,.append", function (e, row) {
                var num = $('.btn-append').parents().children(".form-inline").length;
                var container = $(this).closest("dl");
                var index = container.data("index");
                var name = container.data("name");
                var template = container.data("template");
                var data = container.data();
                // console.log(template);
                index = index ? parseInt(index) : num;
                container.data("index", index + 1);
                var row = row ? row : {};
                var vars = {index: index, name: name, data: data, row: row};
                var html = template ? Template(template, vars) : Template.render(Form.config.fieldlisttpl, vars);
                $(html).insertBefore($(this).closest("dd"));
                $(this).trigger("fa.event.appendfieldlist",
                    $(this).closest("dd").prev());
            });
            //移除控制
            $(".fieldlist2", basic_form).on("click", "dd .btn-remove", function () {
                var container = $(this).closest("dl");
                $(this).closest("dd").remove();
                refresh(container.data("name"));
            });
            //拖拽排序
            $("dl.fieldlist2", basic_form).dragsort({
                itemSelector: 'dd',
                dragSelector: ".btn-dragsort",
                dragEnd: function () {
                    refresh($(this).closest("dl").data("name"));
                },
                placeHolderTemplate: "<dd></dd>"
            });
            //渲染数据
            $(".fieldlist2", basic_form).each(function () {

                var container = this;
                var textarea = $("textarea.hide", basic_form);
                if (textarea.val() == '') {
                    return true;
                }
                var template = $(this).data("template");
                var json = {};
                try {
                    json = JSON.parse(textarea.val());
                } catch (e) {
                }
                $.each(json, function (i, j) {
                    $(".btn-append,.append", container).trigger('click', template ? j : {
                        key: i,
                        value: j
                    });
                });
            });
        });
    }
    return Controller;
});