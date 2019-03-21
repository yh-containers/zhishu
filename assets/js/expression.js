var dataFace = [
    {name:"傲慢",msrc:"/assets/face/aoman.gif",temp_var:"aoman"},
    {name:"白眼",msrc:"/assets/face/baiyan.gif",temp_var:"baiyan"},
    {name:"鄙视",msrc:"/assets/face/bishi.gif",temp_var:"bishi"},
    {name:"闭嘴",msrc:"/assets/face/bizui.gif",temp_var:"bizui"},
    {name:"擦汗",msrc:"/assets/face/cahan.gif",temp_var:"cahan"},
    {name:"呲牙",msrc:"/assets/face/ciya.gif",temp_var:"ciya"},
    {name:"打",msrc:"/assets/face/da.gif",temp_var:"da"},
    {name:"大兵",msrc:"/assets/face/dabing.gif",temp_var:"dabing"},
    {name:"大哭",msrc:"/assets/face/daku.gif",temp_var:"daku"},
    {name:"得意",msrc:"/assets/face/deyi.gif",temp_var:"deyi"},
    {name:"饥饿",msrc:"/assets/face/er.gif",temp_var:"er"},
    {name:"发呆",msrc:"/assets/face/fadai.gif",temp_var:"fadai"},
    {name:"发怒",msrc:"/assets/face/fanu.gif",temp_var:"fanu"},
    {name:"奋斗",msrc:"/assets/face/fendou.gif",temp_var:"fendou"},
    {name:"尴尬",msrc:"/assets/face/gangga.gif",temp_var:"gangga"},
    {name:"鼓掌",msrc:"/assets/face/guzhang.gif",temp_var:"guzhang"},
    {name:"哈哈",msrc:"/assets/face/haha.gif",temp_var:"haha"},
    {name:"害羞",msrc:"/assets/face/haixiu.gif",temp_var:"haixiu"},
    {name:"哈欠",msrc:"/assets/face/haqian.gif",temp_var:"haqian"},

    {name:"坏笑",msrc:"/assets/face/huaixiao.gif",temp_var:"huaixiao"},
    {name:"惊恐",msrc:"/assets/face/jingkong.gif",temp_var:"jingkong"},
    {name:"惊讶",msrc:"/assets/face/jingya.gif",temp_var:"jingya"},
    {name:"可爱",msrc:"/assets/face/keai.gif",temp_var:"keai"},
    {name:"可怜",msrc:"/assets/face/kelian.gif",temp_var:"kelian"},
    {name:"酷",msrc:"/assets/face/ku.gif",temp_var:"ku"},
    {name:"快哭了",msrc:"/assets/face/kuaikule.gif",temp_var:"kuaikule"},
    {name:"困",msrc:"/assets/face/kun.gif",temp_var:"kun"},
    {name:"流汗",msrc:"/assets/face/liuhan.gif",temp_var:"liuhan"},
    {name:"流泪",msrc:"/assets/face/liulei.gif",temp_var:"liulei"},
    {name:"骂",msrc:"/assets/face/ma.gif",temp_var:"ma"},
    {name:"难过",msrc:"/assets/face/nanguo.gif",temp_var:"nanguo"},
    {name:"撇嘴",msrc:"/assets/face/pizui.gif",temp_var:"pizui"},
    {name:"亲亲",msrc:"/assets/face/qinqin.gif",temp_var:"qinqin"},
    {name:"糗大了",msrc:"/assets/face/qioudale.gif",temp_var:"qioudale"},
    {name:"色",msrc:"/assets/face/se.gif",temp_var:"se"},
    {name:"调皮",msrc:"/assets/face/tiaopi.gif",temp_var:"tiaopi"},
    {name:"偷笑",msrc:"/assets/face/touxiao.gif",temp_var:"touxiao"},
    {name:"挖鼻",msrc:"/assets/face/wabi.gif",temp_var:"wabi"},
    {name:"委屈",msrc:"/assets/face/weiqu.gif",temp_var:"weiqu"},

    {name:"微笑",msrc:"/assets/face/weixiao.gif",temp_var:"weixiao"},
    {name:"问",msrc:"/assets/face/wen.gif",temp_var:"wen"},
    {name:"吓",msrc:"/assets/face/xia.gif",temp_var:"xia"},
    {name:"嘘~",msrc:"/assets/face/xu.gif",temp_var:"xu"},
    {name:"晕",msrc:"/assets/face/yun.gif",temp_var:"yun"},
    {name:"再见",msrc:"/assets/face/zaijian.gif",temp_var:"zaijian"},
    {name:"折磨",msrc:"/assets/face/zhemo.gif",temp_var:"zhemo"},
    {name:"抓狂",msrc:"/assets/face/zhuakuang.gif",temp_var:"zhuakuang"},
    {name:"挖鼻",msrc:"/assets/face/wabi.gif",temp_var:"wabi"},
    {name:"猪头",msrc:"/assets/face/zhutou.gif",temp_var:"zhutou"},
    {name:"睡觉",msrc:"/assets/face/shuijiao.gif",temp_var:"shuijiao"}
];



var pic_obj = {};
window.onload=function(){
//        var div = document.getElementById("div");
//        div.innerHTML+="<img src='/assets/face/1.gif'>";

    var editor = document.getElementById("meditor");

    var ul = document.createElement("ul");
    var ulHtml = "";
    for(var i = 0,l= dataFace.length;i<l;i++){
        ulHtml +="<li><img alt='"+dataFace[i].name+"' src='"+dataFace[i].msrc+"' temp_var='"+dataFace[i].temp_var+"' /></li>";
        pic_obj[dataFace[i].temp_var] = "<img alt='"+dataFace[i].name+"' src='"+dataFace[i].msrc+"' temp_var='"+dataFace[i].temp_var+"' />"
    }
    ul.innerHTML=ulHtml;
    editor.insertBefore(ul, editor.getElementsByTagName("div")[0]);


    /*editor.getElementsByTagName("i")[0].onclick=function(){
        editor.getElementsByTagName("ul")[0].style.display = "block";
    };*/

    $(".mcont a").click(function(){
      $(this).siblings().toggle();
    });

    var div = document.getElementById("div");
    var lis = editor.getElementsByTagName("li");
    for(var i = 0, l = lis.length; i<l;i++){
        lis[i].onclick = new function(){
            var choose = lis[i];
            return function(){
                editor.getElementsByTagName("ul")[0].style.display = "none";

                // var value = choose.getElementsByTagName("img")[0].src;
                // var temp_var = choose.getElementsByTagName("img")[0].temp_var;
                // div.innerHTML+="<img src='"+value+"' data-temp_var='"+temp_var+"'>";
                div.innerHTML += $(choose).html()
            }
        };
    }


};
//处理发送过滤标签
var filterHTMLTag = function (msg) {
    var msg = msg.replace(/<\/?[^>]*temp_var="([^"]+)"[^>]*>/g, function (match,param,offset,string) {
        return '['+param+']';
    }); //去除HTML Tag
    msg = msg.replace(/[|]*\n/, '') //去除行尾空格
    msg = msg.replace(/&npsp;/ig, ''); //去掉npsp
    return msg;
}

//显示添加标签
var showHtmlTag = function(msg){
    var msg = msg.replace(/\[([^\]]+)\]/g, function (match,param,offset,string) {
        return pic_obj.hasOwnProperty(param)?pic_obj[param]:match;
    });
    return msg;
}



