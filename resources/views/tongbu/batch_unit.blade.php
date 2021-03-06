<link href="../../admin/assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
<link href="../../admin/assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
<script src="../../admin/assets/global/plugins/icheck/icheck.min.js"></script>
<script src="../../admin/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../../assets/plugins/jqmeter.min.js" type="text/javascript"></script>
<link href="../../admin/assets/global/css/components.css" id="style_components"/>

<script src="../../admin/assets/admin/pages/scripts/form-fileupload.js"></script>
<script src="../../admin/assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
<link href="../../admin/assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css"
      rel="stylesheet"/>
<link href="../../admin/assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
<link href="../../admin/assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>
<script src="{{ URL::asset('assets/plugins/freezeheader/js/jquery.freezeheader.js') }}"></script>
<style>
    .actions {
        float: inherit;
        padding: 0px 0;
    }

    .radio-inline {
        position: relative;
        display: inline-block;
        /* padding-left: 0px;*/
        margin-bottom: 0;
        font-weight: 400;
        vertical-align: middle;
    }
</style>

<script>
    $(document).ready(function () {
        $("#min_table").freezeHeader({'height': '400px'});
    });
</script>
<div class="row" id="unit_title">
    <div class="col-sm-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet box grey">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-user"></i>批量编辑最小起订量</div>
            </div>
            <div class="portlet-body">

                <table class="table table-striped table-bordered table-hover" id="min_table">
                    <thead>
                    <tr style="background-color: #EEEEEE">
                        <th width="5%">产品编号</th>
                        <th>信息标题</th>
                        <th>计量单位</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd gradeX" v-for="list in lists">
                        <td>@{{list.productID}}</td>
                        <td>@{{list.subject}}</td>
                        <td>@{{list.unit}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="portlet box grey" style="margin-bottom: 0px;">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i> 批量编辑最小起订量_FOB一口价
                </div>
            </div>
            <div class="portlet-body form">
                <form role="form">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-1 control-label">最小计量单位</label>
                            <div class="col-md-2">
                                <select class="form-control" id="unit">
                                    <option value="">最小计量单位</option>
                                    <option v-for="unit in units" value="@{{unit.name}}">@{{unit.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="form-actions" style="text-align: right">
            <button type="button" v-on:click="save()" class="btn blue">保存</button>
            <button type="button" v-on:click="putbox()" class="btn blue">存入草稿箱</button>
            <button type="button" v-on:click="batchfabu()" class="btn blue">发布</button>
            <button type="button" class="btn default">返回</button>
        </div>
    </div>
</div>
<script>
    var ids = "";
    $(document).ready(function () {
        //Metronic.init();
    });
    var vm7 = new Vue({
        http: {
            headers: {
                'X-CSRF-TOKEN': document.getElementsByTagName('meta')['_token'].content
            },
        },
        el: '#unit_title',
        data: {
            lists: '',
            units: '',
            keys: ''
        },
        ready: function () {
            this.list();
            this.unitlist();
        },
        methods: {
            list: function () {
                var index = layer.msg('数据加载中...', {icon: 16, time: false});
                this.$http.get('/getbatchunitList', function (data) {
                    layer.close(index);
                    this.$set('lists', eval(data));
                })
            },
            unitlist: function () {
                this.$http.get('/getunitlist', function (data) {
                    this.$set('units', eval(data));
                })
            },
            save: function () {
                unit = $("#unit").val();
                if (unit == '') {
                    Alert5('请选择计量单位');
                }
                senddata = {
                    unit: unit
                };
                var index = layer.msg('数据保存中...', {icon: 16, time: false});
                this.$http.post('/saveunit', senddata, function (data) {
                    if (eval(data).code) {
                        this.list();
                        Alert1('保存成功');
                        layer.close(index);
                    }
                })
            },
            fabu: function () {
                var type = $("input[name='type']:checked").val();
                var index = layer.msg('发布中...', {icon: 16, time: false});
                senddata = {
                    type: type
                };
                this.$http.get('/batchFabu', senddata, function (data) {
                    layer.close(index);
                    if (eval(data).code) {
                        Alert1('编辑发布成功');
                    }
                })
            },
            batchfabu: function () {
                var index = layer.msg('编辑发布中...', {icon: 16, time: false});
                this.$http.get('/batchfabu', function (data) {
                    if (eval(data).code) {
                        layer.close(index);
                        Alert1('编辑发布成功');

                    }
                })
            },
            putbox: function () {
                layer.confirm('放入草稿箱后,产品将不会在这里出现,确定放入吗 ？', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var index = layer.msg('数据处理中...', {icon: 16, time: false});
                    $.get("/putdraftbox", function (result) {
                        if (eval(result).code) {
                            layer.close(index);
                            Alert1('放入成功');
                            func_action('/box');
                        }
                    });
                }, function () {
                    return false;
                });
            },
        },
        watch: {
            cur: function (oldValue, newValue) {
                console.log(arguments)
            }
        }
    })
    Vue.filter('reverse', function (value) {
        if (value == 1) {
            return '男';
        } else {
            return '女';
        }
    })
</script>