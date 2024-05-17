import{b as L,_ as H,$ as K,r as _,f as C,c as E,n as t,l as a,s as m,w as v,E as x,x as P,y as Q,W as R,A as X,z as T,J as Y,v as O,o as Z,k as h,q as N,t as $,a0 as z,B as w,F as ee,N as ae,P as te,a1 as le}from"./.pnpm-DD7z4_75.js";import{i as U}from"./index-Bwb1Kxoh.js";import{_ as oe}from"./_plugin-vue_export-helper-DlAUqK2U.js";const ne=n=>U.post("/admin/group",n),ue=n=>U.get(`/admin/group?page=${n.page}&size=${n.size}`),se=n=>U.patch(`/admin/group/${n.id}`,n),de=n=>U.delete("/admin/group",{data:{group_ids:[n.id]}}),ie=n=>U.delete("/admin/group",{data:{group_ids:n}}),pe=L({__name:"AddGroup",props:{modelValue:{},modelModifiers:{}},emits:H(["getGroups"],["update:modelValue"]),setup(n,{emit:i}){const b=i,y=K(n,"modelValue"),f=_(!1),s=_({name:"",count:0,size:0}),p=_(null),M={name:[{required:!0,message:"请输入组名",trigger:"blur"}],count:[{required:!0,message:"请输入可解析个数",trigger:"blur"}],size:[{required:!0,message:"请输入可解析大小",trigger:"blur"}]},S=async V=>{if(!(!V||!await V.validate()))try{f.value=!0,await ne(s.value),x.success("添加成功")}finally{f.value=!1}},F=V=>{b("getGroups"),V()},B=()=>{y.value=!1,b("getGroups")};return(V,u)=>{const G=P,k=Q,l=R,o=X,c=T,r=Y,D=O;return C(),E(r,{title:"添加用户组",width:"60%",modelValue:y.value,"onUpdate:modelValue":u[5]||(u[5]=d=>y.value=d),"before-close":F},{footer:t(()=>[a(c,{type:"info",onClick:u[3]||(u[3]=d=>B())},{default:t(()=>[m("取消")]),_:1}),a(c,{type:"primary",onClick:u[4]||(u[4]=d=>S(p.value))},{default:t(()=>[m("添加")]),_:1})]),default:t(()=>[v((C(),E(o,{ref_key:"addGroupFormRef",ref:p,model:s.value,rules:M,"label-width":"auto"},{default:t(()=>[a(k,{label:"组名",prop:"name"},{default:t(()=>[a(G,{modelValue:s.value.name,"onUpdate:modelValue":u[0]||(u[0]=d=>s.value.name=d)},null,8,["modelValue"])]),_:1}),a(k,{label:"可解析个数",prop:"count"},{default:t(()=>[a(l,{modelValue:s.value.count,"onUpdate:modelValue":u[1]||(u[1]=d=>s.value.count=d)},null,8,["modelValue"])]),_:1}),a(k,{label:"可解析大小",prop:"size"},{default:t(()=>[a(l,{modelValue:s.value.size,"onUpdate:modelValue":u[2]||(u[2]=d=>s.value.size=d)},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[D,f.value]])]),_:1},8,["modelValue"])}}}),re=L({__name:"GroupManagement",setup(n){const i=_(!1),b=_(15),y=_(1),f=_(),s=_([]),p=async()=>{try{i.value=!0;const l=await ue({page:y.value,size:b.value});f.value=l.data}finally{i.value=!1}},M=async l=>{l.edit=!(l.edit??!1)},S=async l=>{l.edit=!(l.edit??!0),await F(l)},F=async l=>{try{i.value=!0,await se(l),x.success("修改用户组成功")}finally{i.value=!1,await p()}},B=async l=>{try{i.value=!0,await de(l),x.success("删除用户组成功")}finally{i.value=!1,await p()}},V=async()=>{try{i.value=!0;const l=s.value.map(o=>o.id);await ie(l),x.success("批量删除用户组成功")}finally{i.value=!1,await p()}},u=l=>s.value=l;Z(p);const G=_(!1),k=()=>G.value=!G.value;return(l,o)=>{var A,I,q;const c=T,r=ae,D=P,d=R,J=te,W=le,j=O;return C(),h(ee,null,[a(pe,{onGetGroups:p,modelValue:G.value,"onUpdate:modelValue":o[0]||(o[0]=e=>G.value=e)},null,8,["modelValue"]),a(c,{type:"primary",onClick:o[1]||(o[1]=e=>p())},{default:t(()=>[m("刷新列表")]),_:1}),a(c,{type:"primary",onClick:o[2]||(o[2]=e=>k())},{default:t(()=>[m("添加用户组")]),_:1}),a(c,{type:"danger",disabled:s.value.length<=0,onClick:o[3]||(o[3]=e=>V())},{default:t(()=>[m(" 批量删除 ")]),_:1},8,["disabled"]),v((C(),E(J,{data:((A=f.value)==null?void 0:A.data)??[],border:"","show-overflow-tooltip":"",class:"table",onSelectionChange:u},{default:t(()=>[a(r,{type:"selection",width:"40"}),a(r,{prop:"id",label:"ID"}),a(r,{prop:"name",label:"组名"},{default:t(({row:e})=>[v(N("span",null,$(e.name),513),[[z,!e.edit]]),v(a(D,{modelValue:e.name,"onUpdate:modelValue":g=>e.name=g},null,8,["modelValue","onUpdate:modelValue"]),[[z,e.edit]])]),_:1}),a(r,{prop:"count",label:"可解析次数"},{default:t(({row:e})=>[v(N("span",null,$(e.count),513),[[z,!e.edit]]),v(a(d,{modelValue:e.count,"onUpdate:modelValue":g=>e.count=g},null,8,["modelValue","onUpdate:modelValue"]),[[z,e.edit]])]),_:1}),a(r,{prop:"size",label:"可解析大小"},{default:t(({row:e})=>[v(N("span",null,$(e.size),513),[[z,!e.edit]]),v(a(d,{modelValue:e.size,"onUpdate:modelValue":g=>e.size=g},null,8,["modelValue","onUpdate:modelValue"]),[[z,e.edit]])]),_:1}),a(r,{prop:"created_at",label:"创建时间"},{default:t(({row:e})=>[m($(new Date(e.created_at).toLocaleString()),1)]),_:1}),a(r,{prop:"updated_at",label:"更新时间"},{default:t(({row:e})=>[m($(new Date(e.updated_at).toLocaleString()),1)]),_:1}),a(r,{width:"150",label:"操作",fixed:"right"},{default:t(({row:e})=>[e.edit?w("",!0):(C(),E(c,{key:0,size:"small",type:"primary",onClick:g=>M(e)},{default:t(()=>[m(" 编辑 ")]),_:2},1032,["onClick"])),e.edit?(C(),E(c,{key:1,size:"small",type:"primary",onClick:g=>S(e)},{default:t(()=>[m(" 保存 ")]),_:2},1032,["onClick"])):w("",!0),a(c,{size:"small",type:"danger",onClick:g=>B(e)},{default:t(()=>[m("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])),[[j,i.value]]),a(W,{"current-page":y.value,"onUpdate:currentPage":o[4]||(o[4]=e=>y.value=e),"page-size":b.value,"onUpdate:pageSize":o[5]||(o[5]=e=>b.value=e),"page-sizes":[15,50,100,500,((I=f.value)==null?void 0:I.total)??100],total:((q=f.value)==null?void 0:q.total)??100,layout:"sizes, prev, pager, next",onSizeChange:p,onCurrentChange:p},null,8,["current-page","page-size","page-sizes","total"])],64)}}}),fe=oe(re,[["__scopeId","data-v-87de4841"]]);export{fe as default};
