import{b as P,r as u,o as T,e as k,j as A,k as e,m as a,x as n,t as i,u as E,w as F,c as N,F as j,E as S,A as G,G as J,I as O,J as q,S as H,U as K,a2 as Q,v as W}from"./.pnpm-DQd76FlD.js";import{i as g}from"./index-BlJRWrpm.js";import{f as B}from"./format-DaCfCw6f.js";import{_ as X}from"./_plugin-vue_export-helper-DlAUqK2U.js";const Y=d=>g.get(`/admin/record?page=${d.page}&size=${d.size}&orderBy=${d.orderBy}`),Z=()=>g.get("/admin/record/count"),ee=d=>g.delete("/admin/record",{data:{record_ids:[d.id]}}),te=d=>g.delete("/admin/record",{data:{record_ids:d}}),ae=P({__name:"RecordManagement",setup(d){const r=u(!1),v=u(15),f=u(1),m=u("id"),_=u(),y=u([]),p=u({today:{count:0,size:0},total:{count:0,size:0}}),c=async()=>{try{r.value=!0;const s=await Y({page:f.value,size:v.value,orderBy:m.value});_.value=s.data}finally{r.value=!1,await D()}},D=async()=>{try{r.value=!0;const s=await Z();p.value=s.data}finally{r.value=!1}},I=async s=>{try{r.value=!0,await ee(s),S.success("删除记录成功")}finally{r.value=!1,await c()}},$=async()=>{try{r.value=!0;const s=y.value.map(l=>l.id);await te(s),S.success("删除记录成功")}finally{r.value=!1,await c()}},U=s=>y.value=s;return T(c),(s,l)=>{var C,x,R;const b=G,z=J,w=O,V=q,o=H,h=K,L=Q,M=W;return k(),A(j,null,[e(b,{type:"primary",onClick:l[0]||(l[0]=t=>c())},{default:a(()=>[n("刷新列表")]),_:1}),e(b,{type:"danger",disabled:y.value.length<=0,onClick:l[1]||(l[1]=t=>$())},{default:a(()=>[n(" 批量删除 ")]),_:1},8,["disabled"]),e(z,{style:{"margin-left":"20px"}},{default:a(()=>[n(" 累计解析: "+i(p.value.total.count)+" ("+i(E(B)(p.value.total.size))+") ",1)]),_:1}),e(z,null,{default:a(()=>[n(" 今日解析: "+i(p.value.today.count)+" ("+i(E(B)(p.value.today.size))+") ",1)]),_:1}),e(z,{style:{"margin-left":"20px"}},{default:a(()=>[n(" 按照 "),e(V,{modelValue:m.value,"onUpdate:modelValue":l[2]||(l[2]=t=>m.value=t),onChange:c,style:{width:"100px"}},{default:a(()=>[e(w,{key:"id",label:"时间",value:"id"}),e(w,{key:"size",label:"文件大小",value:"size"})]),_:1},8,["modelValue"]),n(" 排序 ")]),_:1}),F((k(),N(h,{data:((C=_.value)==null?void 0:C.data)??[],border:"","show-overflow-tooltip":"",class:"table",onSelectionChange:U},{default:a(()=>[e(o,{type:"selection",width:"40"}),e(o,{prop:"id",label:"ID"}),e(o,{prop:"ip",label:"IP"}),e(o,{prop:"fs_id",label:"文件ID"}),e(o,{prop:"url",label:"下载链接"}),e(o,{prop:"ua",label:"UA"}),e(o,{prop:"user_id",label:"用户ID"},{default:a(({row:t})=>[n(i(t.user_id??"非用戶解析"),1)]),_:1}),e(o,{prop:"token_id",label:"卡密ID"},{default:a(({row:t})=>[n(i(t.token_id??"非卡密解析"),1)]),_:1}),e(o,{prop:"account_id",label:"解析账号ID"}),e(o,{prop:"created_at",label:"创建时间"},{default:a(({row:t})=>[n(i(new Date(t.created_at).toLocaleString()),1)]),_:1}),e(o,{prop:"updated_at",label:"更新时间"},{default:a(({row:t})=>[n(i(new Date(t.updated_at).toLocaleString()),1)]),_:1}),e(o,{width:"150",label:"操作",fixed:"right"},{default:a(({row:t})=>[e(b,{size:"small",type:"danger",onClick:le=>I(t)},{default:a(()=>[n("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])),[[M,r.value]]),e(L,{"current-page":f.value,"onUpdate:currentPage":l[3]||(l[3]=t=>f.value=t),"page-size":v.value,"onUpdate:pageSize":l[4]||(l[4]=t=>v.value=t),"page-sizes":[15,50,100,500,((x=_.value)==null?void 0:x.total)??100],total:((R=_.value)==null?void 0:R.total)??100,layout:"sizes, prev, pager, next",onSizeChange:c,onCurrentChange:c},null,8,["current-page","page-size","page-sizes","total"])],64)}}}),de=X(ae,[["__scopeId","data-v-8f43fd71"]]);export{de as default};