import{b as P,J as B,o as q,L as J,E,u as j,r as H,w as K,e,f as u,c as m,n as r,q as S,t as O,C as c,l as s,k as U,F as h,x as y,M as Q,y as W,z as X,A as Y,B as ee,p as te,v as le}from"./.pnpm-CdrKUIM1.js";import{u as oe}from"./fileListStore-DrsdbouG.js";import{u as se,a as ae,g as A,c as re}from"./index-C7F-zFvO.js";import{c as ne}from"./copy-Bloa_NrV.js";import{f as ie}from"./format-DaCfCw6f.js";import{_ as ue}from"./_plugin-vue_export-helper-DlAUqK2U.js";const de=["src"],pe=P({__name:"GetFileList",setup(me){const f=oe(),{pending:R,getFileListForm:l,getFileListFormRef:V,selectedRows:x,limitForm:w,limitMessage:b,vcode:g}=B(f),L=se(),{config:_}=B(L),z=(o,t,n)=>t===""?n(new Error("请先输入需要解析的链接")):$(t)?n():n(new Error("请输入合法的链接")),D=()=>{l.value.dir="/",l.value.surl="";const o=$(l.value.url);o&&(o.id&&(o.surl?(l.value.url=`https://pan.baidu.com/share/init?surl=${o.id}`,l.value.surl=`1${o.id}`):(l.value.url=`https://pan.baidu.com/s/${o.id}`,l.value.surl=o.id)),o.pwd&&(l.value.pwd=o.pwd,E.success("已自动填写密码")))},$=o=>{const t=o.match(/s\/([a-zA-Z0-9_-]+)/),n=o.match(/surl=([a-zA-Z0-9_-]+)/),d=o.match(/\?pwd=([a-zA-Z0-9_-]+)/),i=o.match(/&pwd=([a-zA-Z0-9_-]+)/),p=o.match(/提取码:\s?([a-zA-Z0-9_-]+)/);let v;if(n)v=n[1];else if(t)v=t[1];else return!1;const k=d?d[1]:i?i[1]:p?p[1]:null;return n?{surl:!0,id:v,pwd:k}:{id:v,pwd:k}},N={url:[{required:!0,validator:z,trigger:"blur"}]},T=async o=>{if(!o||!await o.validate())return;const t=new URLSearchParams;t.set("url",l.value.url),t.set("surl",l.value.surl),t.set("pwd",l.value.pwd),t.set("dir",l.value.dir),ne(`${location.host}/?${t.toString()}`,"复制成功")};q(()=>{J(()=>{const o=new URLSearchParams(location.search);o.size<4||(l.value={url:o.get("url")??"",pwd:o.get("pwd")??"",dir:o.get("dir")??"/",surl:o.get("surl")??""},E.success("已读取到参数,正在加载"),setTimeout(f.getFileList,1e3))}),f.getLimit(),C()});const F=j(),Z=()=>F.push("/login"),I=()=>F.push("/admin"),M=H(0),C=()=>M.value=Date.now();return(o,t)=>{const n=Q,d=W,i=X,p=Y,v=ee,k=te,G=le;return K((u(),m(k,null,{default:r(()=>[S("h2",null,"前台解析中心 | "+O(e(ae)()),1),e(_).show_copyright?(u(),m(n,{key:0,"show-icon":"",type:"warning",closable:!1,title:e(_).custom_copyright},null,8,["title"])):c("",!0),e(_).have_account?(u(),m(n,{key:1,closable:!1,class:"alert",title:"当前中转账号充足",type:"success"})):(u(),m(n,{key:2,closable:!1,class:"alert",title:"当前中转账号不足",type:"error"})),e(_).debug?(u(),m(n,{key:3,class:"alert",title:"当前网站开启了DEBUG模式,非调试请关闭!!!!",type:"error",closable:!1})):c("",!0),e(_).is_https?c("",!0):(u(),m(n,{key:4,class:"alert",title:"当前网站未开启SSL,可能出现无法请求Aria2服务器的问题",type:"error",closable:!1})),s(n,{class:"alert",type:e(b)===""?"success":"error",closable:!1,title:e(b)===""?`当前${e(l).token?"卡密":"用户组"}: ${e(w).group_name} 剩余可解析文件数: ${e(w).count} 剩余可解析大小: ${e(ie)(e(w).size)} ${e(l).token?`到期时间: ${e(w).expired_at??"未知"}`:""}`:e(b)??"未知错误"},null,8,["type","title"]),s(v,{ref_key:"getFileListFormRef",ref:V,model:e(l),rules:N,"label-width":"auto",class:"form"},{default:r(()=>[s(i,{label:"链接",prop:"url"},{default:r(()=>[s(d,{modelValue:e(l).url,"onUpdate:modelValue":t[0]||(t[0]=a=>e(l).url=a),modelModifiers:{trim:!0},onBlur:t[1]||(t[1]=a=>D())},null,8,["modelValue"])]),_:1}),s(i,{label:"密码",prop:"pwd"},{default:r(()=>[s(d,{modelValue:e(l).pwd,"onUpdate:modelValue":t[2]||(t[2]=a=>e(l).pwd=a),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1}),e(_).need_password?(u(),m(i,{key:0,label:"解析密码",prop:"password"},{default:r(()=>[s(d,{modelValue:e(l).password,"onUpdate:modelValue":t[3]||(t[3]=a=>e(l).password=a),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1})):c("",!0),s(i,{label:"卡密",prop:"token"},{default:r(()=>[s(d,{modelValue:e(l).token,"onUpdate:modelValue":t[4]||(t[4]=a=>e(l).token=a),modelModifiers:{trim:!0},onBlur:e(f).getLimit},null,8,["modelValue","onBlur"])]),_:1}),s(i,{label:"当前路径",prop:"dir"},{default:r(()=>[s(d,{modelValue:e(l).dir,"onUpdate:modelValue":t[5]||(t[5]=a=>e(l).dir=a),disabled:""},null,8,["modelValue"])]),_:1}),e(g).hit_captcha?(u(),U(h,{key:1},[s(i,{label:"验证码编号",prop:"vcode_str"},{default:r(()=>[s(d,{modelValue:e(g).vcode_str,"onUpdate:modelValue":t[6]||(t[6]=a=>e(g).vcode_str=a),disabled:""},null,8,["modelValue"])]),_:1}),s(i,{label:"验证码图片",prop:"vcode_img"},{default:r(()=>[S("img",{src:`${e(g).vcode_img}&t=${M.value}`,alt:"验证码图片",onClick:C},null,8,de)]),_:1}),s(i,{label:"验证码字符",prop:"vcode_input"},{default:r(()=>[s(d,{modelValue:e(g).vcode_input,"onUpdate:modelValue":t[7]||(t[7]=a=>e(g).vcode_input=a)},null,8,["modelValue"])]),_:1})],64)):c("",!0),s(i,{label:" "},{default:r(()=>[s(p,{type:"primary",onClick:t[8]||(t[8]=a=>e(f).getFileList())},{default:r(()=>[y("获取/刷新列表")]),_:1}),s(p,{type:"primary",disabled:e(x).length<=0,onClick:t[9]||(t[9]=a=>e(f).getDownloadLinks())},{default:r(()=>[y(" 批量解析 ")]),_:1},8,["disabled"]),s(p,{type:"primary",onClick:t[10]||(t[10]=a=>T(e(V)))},{default:r(()=>[y("复制当前地址")]),_:1}),e(A)()==="0"?(u(),m(p,{key:0,type:"primary",onClick:t[11]||(t[11]=a=>Z())},{default:r(()=>[y("登陆")]),_:1})):c("",!0),e(A)()==="1"?(u(),U(h,{key:1},[e(re)()==="admin"?(u(),m(p,{key:0,type:"primary",onClick:t[12]||(t[12]=a=>I())},{default:r(()=>[y(" 进入后台 ")]),_:1})):c("",!0),s(p,{type:"danger",onClick:t[13]||(t[13]=a=>e(L).logout())},{default:r(()=>[y(" 注销 ")]),_:1})],64)):c("",!0)]),_:1})]),_:1},8,["model"])]),_:1})),[[G,e(R)]])}}}),we=ue(pe,[["__scopeId","data-v-56056bbb"]]);export{we as default};
