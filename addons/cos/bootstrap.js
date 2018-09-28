//修改上传的接口调用
require(['upload', '../addons/cos/js/spark'], function (Upload, SparkMD5) {
    var _onFileAdded = Upload.events.onFileAdded;
    var _onUploadResponse = Upload.events.onUploadResponse;
    var _process = function (up, file) {
        (function (up, file) {
            var blob = file.getNative();
            var loadedBytes = file.loaded;
            var chunkSize = 2097152;
            var chunkBlob = blob.slice(loadedBytes, loadedBytes + chunkSize);
            var reader = new FileReader();
            reader.addEventListener('loadend', function (e) {
                var spark = new SparkMD5.ArrayBuffer();
                spark.append(e.target.result);
                var md5 = spark.end();
                Fast.api.ajax({
                    url: "/addons/cos/index/params",
                    data: {method: 'POST', md5: md5, name: file.name, type: file.type, size: file.size},
                }, function (data) {
                    file.md5 = md5;
                    file.status = 1;
                    file.key = data.key;
                    file.filename = data.filename;
                    file.token = data.token;
                    file.signature = data.signature;
                    file.notifysignature = data.notifysignature;
                    up.start();
                    return false;
                });
                return;
            });
            reader.readAsArrayBuffer(chunkBlob);
        })(up, file);
    };
    Upload.events.onFileAdded = function (up, files) {
        return _onFileAdded.call(this, up, files);
    };
    Upload.events.onBeforeUpload = function (up, file) {
        if (typeof file.md5 === 'undefined') {
            up.stop();
            _process(up, file);
        } else {
            up.settings.headers = up.settings.headers || {};
            up.settings.multipart_params.key = file.key;
            up.settings.multipart_params.Signature = file.signature;
            up.settings.multipart_params.success_action_status = 200;
            up.settings.multipart_params['Content-Disposition'] = 'inline; filename=' + file.filename;
            up.settings.multipart_params['Content-Type'] = file.type;
            up.settings.multipart_params['x-cos-security-token'] = file.token;
            up.settings.send_file_name = false;
        }
    };
    Upload.events.onUploadResponse = function (response, info, up, file) {
        try {
            var ret = {};
            if (info.status === 200) {
                var url = '/' + file.key;
                Fast.api.ajax({
                    url: "/addons/cos/index/notify",
                    data: {
                        method: 'POST',
                        name: file.name,
                        url: url,
                        md5: file.md5,
                        size: file.size,
                        type: file.type,
                        signature: file.signature,
                        token: file.token,
                        notifysignature: file.notifysignature
                    }
                }, function () {
                    return false;
                });
                ret.code = 1;
                ret.data = {
                    url: url
                };
            } else {
                ret.code = 0;
                ret.msg = info.response;
            }
            return _onUploadResponse.call(this, JSON.stringify(ret));

        } catch (e) {
        }
        return _onUploadResponse.call(this, response);

    };
});