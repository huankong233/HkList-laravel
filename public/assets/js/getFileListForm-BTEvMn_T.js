import"./base-BR_-BsM2.js";import{v as R}from"./el-loading-d7Mws69L.js";import{E as D}from"./el-card-BodEXr2D.js";import{E as Z,a as q}from"./el-form-item-CvLlMsKf.js";import{E as G}from"./el-button-BnaFODCY.js";import{E as O}from"./el-input-XOK5QbSw.js";import{a as B,M as Q,r as j,g as M,e as n,R as p,w as i,P as I,p as A,n as _,u as e,a5 as H,i as y,c as S,h as U,q as b,z as $,F as J,b as a,Q as K,a3 as W,k as X,A as Y,y as x}from"./index-B7TNvURf.js";import{u as ee}from"./UserPannel-l6PqsLCr.js";import{c as te}from"./copy-LJRJ3JFf.js";import{b as le,r as se,T as N,u as oe,f as z,_ as re,s as ae,w as ie,E as ne}from"./request--9q6OgJM.js";import"./use-form-item-DYh_CRjk.js";import"./_initCloneObject-yxA5fZ3x.js";import"./user-C7PWTPPF.js";const de=["light","dark"],ue=le({title:{type:String,default:""},description:{type:String,default:""},type:{type:String,values:se(N),default:"info"},closable:{type:Boolean,default:!0},closeText:{type:String,default:""},showIcon:Boolean,center:Boolean,effect:{type:String,values:de,default:"light"}}),pe={close:C=>C instanceof MouseEvent},me=B({name:"ElAlert"}),ce=B({...me,props:ue,emits:pe,setup(C,{emit:v}){const m=C,{Close:l}=ae,w=Q(),r=oe("alert"),F=j(!0),V=M(()=>N[m.type]),T=M(()=>[r.e("icon"),{[r.is("big")]:!!m.description||!!w.default}]),L=M(()=>({"with-description":m.description||w.default})),E=o=>{F.value=!1,v("close",o)};return(o,s)=>(n(),p(W,{name:e(r).b("fade"),persisted:""},{default:i(()=>[I(A("div",{class:_([e(r).b(),e(r).m(o.type),e(r).is("center",o.center),e(r).is(o.effect)]),role:"alert"},[o.showIcon&&e(V)?(n(),p(e(z),{key:0,class:_(e(T))},{default:i(()=>[(n(),p(H(e(V))))]),_:1},8,["class"])):y("v-if",!0),A("div",{class:_(e(r).e("content"))},[o.title||o.$slots.title?(n(),S("span",{key:0,class:_([e(r).e("title"),e(L)])},[U(o.$slots,"title",{},()=>[b($(o.title),1)])],2)):y("v-if",!0),o.$slots.default||o.description?(n(),S("p",{key:1,class:_(e(r).e("description"))},[U(o.$slots,"default",{},()=>[b($(o.description),1)])],2)):y("v-if",!0),o.closable?(n(),S(J,{key:2},[o.closeText?(n(),S("div",{key:0,class:_([e(r).e("close-btn"),e(r).is("customed")]),onClick:E},$(o.closeText),3)):(n(),p(e(z),{key:1,class:_(e(r).e("close-btn")),onClick:E},{default:i(()=>[a(e(l))]),_:1},8,["class"]))],64)):y("v-if",!0)],2)],2),[[K,F.value]])]),_:3},8,["name"]))}});var fe=re(ce,[["__file","alert.vue"]]);const ye=ie(fe),Le=B({__name:"getFileListForm",setup(C){const v=ee(),{clientConfig:m,getFileListForm:l,getFileListFormRef:w,selectedRows:r}=X(v),F=()=>{const s=V(l.value.url);s&&(s.id&&(s.surl?l.value.url=`https://pan.baidu.com/share/init?surl=${s.id}`:l.value.url=`https://pan.baidu.com/s/${s.id}`),s.pwd&&(l.value.pwd=s.pwd,ne.success("已自动填写密码")))},V=s=>{const t=s.match(/s\/([a-zA-Z0-9_-]+)/),u=s.match(/surl=([a-zA-Z0-9_-]+)/),f=s.match(/\?pwd=([a-zA-Z0-9_-]+)/),c=s.match(/&pwd=([a-zA-Z0-9_-]+)/),g=s.match(/提取码:\s?([a-zA-Z0-9_-]+)/);let k;if(u)k=u[1];else if(t)k=t[1];else return!1;const h=f?f[1]:c?c[1]:g?g[1]:null;return u?{surl:!0,id:k,pwd:h}:{id:k,pwd:h}},L={url:[{required:!0,validator:(s,t,u)=>t===""?u(new Error("请先输入需要解析的链接")):V(t)?u():u(new Error("请输入合法的链接")),trigger:"blur"}]},E=async s=>{s&&await s.validate(()=>{})&&(l.value.pending=!0,await v.getFileList()&&await v.getFileSign(),l.value.pending=!1)},o=async s=>{s&&await s.validate(()=>{})&&te(`${location.host}/?url=${l.value.url}&pwd=${l.value.pwd}&dir=${l.value.dir}`,"复制成功")};return(s,t)=>{const u=ye,f=O,c=Z,g=G,k=q,h=D,P=R;return I((n(),p(h,null,{default:i(()=>[A("h2",null,"前台解析中心 | "+$(e(Y)()),1),e(m).haveAccount?(n(),p(u,{key:0,title:"当前中转账号充足",type:"success"})):(n(),p(u,{key:1,title:"当前中转账号不足",type:"error"})),e(m).debug?(n(),p(u,{key:2,class:"alert",title:"当前网站开启了DEBUG模式,非调试请关闭!!!!",type:"error"})):y("",!0),e(m).ishttps?y("",!0):(n(),p(u,{key:3,class:"alert",title:"当前网站未开启SSL,可能出现无法请求Aria2服务器的问题",type:"error"})),a(k,{ref_key:"getFileListFormRef",ref:w,model:e(l),rules:L,"label-width":"auto",class:"form"},{default:i(()=>[a(c,{label:"链接",prop:"url"},{default:i(()=>[a(f,{modelValue:e(l).url,"onUpdate:modelValue":t[0]||(t[0]=d=>e(l).url=d),modelModifiers:{trim:!0},onBlur:t[1]||(t[1]=d=>F())},null,8,["modelValue"])]),_:1}),a(c,{label:"密码",prop:"pwd"},{default:i(()=>[a(f,{modelValue:e(l).pwd,"onUpdate:modelValue":t[2]||(t[2]=d=>e(l).pwd=d),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1}),e(m).havePassword?(n(),p(c,{key:0,label:"解析密码",prop:"password"},{default:i(()=>[a(f,{modelValue:e(l).password,"onUpdate:modelValue":t[3]||(t[3]=d=>e(l).password=d),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1})):y("",!0),e(x)()==="1"?(n(),p(c,{key:1,label:"指定用户解析",prop:"bd_user_id"},{default:i(()=>[a(f,{modelValue:e(l).bd_user_id,"onUpdate:modelValue":t[4]||(t[4]=d=>e(l).bd_user_id=d),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1})):y("",!0),a(c,{label:"当前路径",prop:"dir"},{default:i(()=>[a(f,{modelValue:e(l).dir,"onUpdate:modelValue":t[5]||(t[5]=d=>e(l).dir=d),disabled:""},null,8,["modelValue"])]),_:1}),a(c,{label:" "},{default:i(()=>[a(g,{type:"primary",onClick:t[6]||(t[6]=d=>E(e(w)))},{default:i(()=>[b(" 解析链接 ")]),_:1}),a(g,{type:"primary",onClick:t[7]||(t[7]=d=>e(v).freshFileList(e(w)))},{default:i(()=>[b(" 刷新列表 ")]),_:1}),a(g,{type:"primary",disabled:e(r).length<=0,onClick:e(v).downloadFiles},{default:i(()=>[b(" 批量解析 ")]),_:1},8,["disabled","onClick"]),a(g,{type:"primary",onClick:t[8]||(t[8]=d=>o(e(w)))},{default:i(()=>[b(" 复制当前地址 ")]),_:1})]),_:1})]),_:1},8,["model"])]),_:1})),[[P,e(m).pending||e(l).pending]])}}});export{Le as default};
