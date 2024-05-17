import{b as P,_ as j,$ as H,r as _,f as b,c as C,n as l,l as e,s as c,w as M,E as m,D as K,y as O,x as Q,A as W,z as T,J as X,v as R,o as Y,k as Z,q as ee,t as k,a0 as te,F as ae,N as le,P as ne,a1 as oe}from"./.pnpm-DD7z4_75.js";import{i as f}from"./index-Bwb1Kxoh.js";import{_ as se}from"./_plugin-vue_export-helper-DlAUqK2U.js";const ce=s=>f.post("/admin/account",s),ue=s=>f.get(`/admin/account?page=${s.page}&size=${s.size}`),ie=s=>f.patch("/admin/account",{account_ids:[s.id]}),de=s=>f.patch("/admin/account",{account_ids:s}),re=s=>f.delete("/admin/account",{data:{account_ids:[s.id]}}),pe=s=>f.delete("/admin/account",{data:{account_ids:s}}),I=s=>f.patch("/admin/account/switch",{account_ids:[s.account.id],switch:s.switch}),N=s=>f.patch("/admin/account/switch",s),me=P({__name:"AddAccount",props:{modelValue:{},modelModifiers:{}},emits:j(["getAccounts"],["update:modelValue"]),setup(s,{emit:o}){const A=o,y=H(s,"modelValue"),v=_(!1),d=_({cookie:""}),u=_(null),V={cookie:[{required:!0,message:"请输入Cookie",trigger:"blur"}]},S=async g=>{if(!(!g||!await g.validate()))try{v.value=!0,(await ce({cookie:d.value.cookie.split(`
`)})).data.have_repeat&&m.info("存在重复的cookie,已自动过滤"),m.success("添加成功")}finally{v.value=!1}},h=g=>{A("getAccounts"),g()},x=()=>{y.value=!1,A("getAccounts")};return(g,p)=>{const E=K,$=O,D=Q,w=W,z=T,n=X,t=R;return b(),C(n,{title:"添加账号",width:"60%",modelValue:y.value,"onUpdate:modelValue":p[3]||(p[3]=i=>y.value=i),"before-close":h},{footer:l(()=>[e(z,{type:"info",onClick:p[1]||(p[1]=i=>x())},{default:l(()=>[c("取消")]),_:1}),e(z,{type:"primary",onClick:p[2]||(p[2]=i=>S(u.value))},{default:l(()=>[c("添加")]),_:1})]),default:l(()=>[M((b(),C(w,{ref_key:"addAccountFormRef",ref:u,model:d.value,rules:V,"label-width":"auto"},{default:l(()=>[e($,{label:"提示"},{default:l(()=>[e(E,null,{default:l(()=>[c("可以使用换行来分割多个账号")]),_:1})]),_:1}),e($,{label:"Cookie",prop:"cookie"},{default:l(()=>[e(D,{type:"textarea",modelValue:d.value.cookie,"onUpdate:modelValue":p[0]||(p[0]=i=>d.value.cookie=i)},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[t,v.value]])]),_:1},8,["modelValue"])}}}),_e=P({__name:"AccountManagement",setup(s){const o=_(!1),A=_(15),y=_(1),v=_(),d=_([]),u=async()=>{try{o.value=!0;const n=await ue({page:y.value,size:A.value});v.value=n.data}finally{o.value=!1}},V=async n=>{try{o.value=!0,await ie(n),m.success("更新账户信息成功")}finally{o.value=!1,await u()}},S=async()=>{try{o.value=!0;const n=d.value.map(t=>t.id);await de(n),m.success("批量更新账户成功")}finally{o.value=!1,await u()}},h=async n=>{try{o.value=!0,await re(n),m.success("删除账户成功")}finally{o.value=!1,await u()}},x=async()=>{try{o.value=!0;const n=d.value.map(t=>t.id);await pe(n),m.success("批量删除账户成功")}finally{o.value=!1,await u()}},g=async n=>{try{o.value=!0,await I({account:n,switch:1}),m.success("启用账户成功")}finally{o.value=!1,await u()}},p=async n=>{try{o.value=!0,await I({account:n,switch:0}),m.success("禁用账户成功")}finally{o.value=!1,await u()}},E=async()=>{try{o.value=!0;const n=d.value.map(t=>t.id);await N({account_ids:n,switch:1}),m.success("批量启用账户成功")}finally{o.value=!1,await u()}},$=async()=>{try{o.value=!0;const n=d.value.map(t=>t.id);await N({account_ids:n,switch:0}),m.success("批量禁用账户成功")}finally{o.value=!1,await u()}},D=n=>d.value=n;Y(u);const w=_(!1),z=()=>w.value=!w.value;return(n,t)=>{var L,B,U;const i=T,r=le,q=ne,G=oe,J=R;return b(),Z(ae,null,[e(me,{onGetAccounts:u,modelValue:w.value,"onUpdate:modelValue":t[0]||(t[0]=a=>w.value=a)},null,8,["modelValue"]),e(i,{type:"primary",onClick:t[1]||(t[1]=a=>u())},{default:l(()=>[c("刷新列表")]),_:1}),e(i,{type:"primary",onClick:t[2]||(t[2]=a=>z())},{default:l(()=>[c("添加账号")]),_:1}),e(i,{type:"primary",disabled:d.value.length<=0,onClick:t[3]||(t[3]=a=>S())},{default:l(()=>[c(" 批量更新信息 ")]),_:1},8,["disabled"]),e(i,{type:"primary",disabled:d.value.length<=0,onClick:t[4]||(t[4]=a=>E())},{default:l(()=>[c(" 批量启用 ")]),_:1},8,["disabled"]),e(i,{type:"primary",disabled:d.value.length<=0,onClick:t[5]||(t[5]=a=>$())},{default:l(()=>[c(" 批量禁用 ")]),_:1},8,["disabled"]),e(i,{type:"danger",disabled:d.value.length<=0,onClick:t[6]||(t[6]=a=>x())},{default:l(()=>[c(" 批量删除 ")]),_:1},8,["disabled"]),M((b(),C(q,{data:((L=v.value)==null?void 0:L.data)??[],border:"","show-overflow-tooltip":"",class:"table",onSelectionChange:D},{default:l(()=>[e(r,{type:"selection",width:"40"}),e(r,{prop:"id",label:"ID"}),e(r,{prop:"baidu_name",label:"百度用户名"}),e(r,{prop:"netdisk_name",label:"网盘用户名"}),e(r,{prop:"cookie",label:"Cookie"}),e(r,{prop:"vip_type",label:"会员类型"}),e(r,{prop:"switch",label:"状态"},{default:l(({row:a})=>[M(ee("span",null,k(a.switch?"启用":"禁用"),513),[[te,!a.edit]])]),_:1}),e(r,{prop:"reason",label:"禁用原因"}),e(r,{prop:"svip_end_at",label:"超级会员结束时间"},{default:l(({row:a})=>[c(k(new Date(a.svip_end_at).toLocaleString()),1)]),_:1}),e(r,{prop:"last_use_at",label:"上次使用时间"},{default:l(({row:a})=>[c(k(new Date(a.last_use_at).toLocaleString()),1)]),_:1}),e(r,{prop:"created_at",label:"创建时间"},{default:l(({row:a})=>[c(k(new Date(a.created_at).toLocaleString()),1)]),_:1}),e(r,{prop:"updated_at",label:"更新时间"},{default:l(({row:a})=>[c(k(new Date(a.updated_at).toLocaleString()),1)]),_:1}),e(r,{width:"220",label:"操作",fixed:"right"},{default:l(({row:a})=>[e(i,{size:"small",type:"primary",onClick:F=>V(a)},{default:l(()=>[c("更新信息")]),_:2},1032,["onClick"]),a.switch===0?(b(),C(i,{key:0,size:"small",type:"primary",onClick:F=>g(a)},{default:l(()=>[c(" 启用 ")]),_:2},1032,["onClick"])):(b(),C(i,{key:1,size:"small",type:"primary",onClick:F=>p(a)},{default:l(()=>[c("禁用")]),_:2},1032,["onClick"])),e(i,{size:"small",type:"danger",onClick:F=>h(a)},{default:l(()=>[c("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])),[[J,o.value]]),e(G,{"current-page":y.value,"onUpdate:currentPage":t[7]||(t[7]=a=>y.value=a),"page-size":A.value,"onUpdate:pageSize":t[8]||(t[8]=a=>A.value=a),"page-sizes":[15,50,100,500,((B=v.value)==null?void 0:B.total)??100],total:((U=v.value)==null?void 0:U.total)??100,layout:"sizes, prev, pager, next",onSizeChange:u,onCurrentChange:u},null,8,["current-page","page-size","page-sizes","total"])],64)}}}),ge=se(_e,[["__scopeId","data-v-9763e042"]]);export{ge as default};
