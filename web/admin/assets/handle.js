var layer;
layui.use(['layer'], function(){
    layer = layui.layer

});
$.common={
    //打开页面
    openUrl(url,title){
        var index = layer.open({
            type:2,
            title:title,
            content:url
        })
        layer.full(index)
    },
    //表单提交
    formSubmit(obj,is_refresh){
        //页面是否刷新
        is_refresh = is_refresh?is_refresh:true;
        var index = layer.load()
        var obj = obj?obj:$("#form")
        $.post(obj.attr('action'),obj.serialize(),function(result){
            layer.close(index)
            layer.msg(result.msg)
            if(is_refresh){
                if(is_refresh===1){
                    setTimeout(function(){window.location.reload()},1000)
                }else{
                    if(result.code==1){
                        window.history.back()
                    }
                }

            }

        })
        return false;
    },
    //删除
    del(url, obj,title){
            title = title?title:'是否删除该数据';
          layer.confirm(title,function(){
              $.get(url, function(result){
                  layer.msg(result.msg)
                  if(result.code==1){
                      //刷新页面
                      setTimeout(function(){location.reload()},1000)
                  }
              })
          })
    },
    //修改
    modify(url, obj,title){
          title = title?title:'是否修改数据';
          obj = Object.assign({},obj)
          layer.confirm(title,function(){
              $.post(url,obj, function(result){
                  layer.msg(result.msg)
                  if(result.code==1){
                      //刷新页面
                      setTimeout(function(){location.reload()},1000)
                  }
              })
          })
    },
    //修改
    confirm(url, obj,title){
          title = title?title:'是否修改数据';
          obj = Object.assign({},obj)
          layer.confirm(title,function(){
              $.post(url,obj, function(result){
                  layer.msg(result.msg)
                  if(result.code==1){
                      //刷新页面
                      setTimeout(function(){location.reload()},1000)
                  }
              })
          })
    },
    //文件上传
    uploadFile(upload,elem,func){
        //执行实例
        var uploadInst = upload.render({
            elem: elem //绑定元素
            ,acceptMime:'image/*'
            ,done: function(res, index, upload){
                //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
                var item = this.item;
                if(func){

                }else{
                    $(item).parent().find('img').attr('src',res.path)
                    $(item).prev().val(res.path)
                }
            }
            ,error: function(){
                //请求异常回调
                layer.msg('上传异常')
            }
        });
    },
    //富文本
    layerEdit(layedit,elem){
        //注意：layedit.set 一定要放在 build 前面，否则配置全局接口将无效。
        return layedit.build(elem,{
            uploadImage: {
                url: '/admin.php/upload/editUpload/type/layerEdit' //接口url
            }
        }); //建立编辑器

    }


}