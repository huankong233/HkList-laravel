import{b as P,Z as j,_ as H,r as v,f as g,c as I,n as l,l as a,s as _,w as m,k as T,F as w,W as J,E as x,X as K,Y as Q,y as h,V as G,x as W,A as ee,z as X,I as ae,v as Y,o as le,q as M,t as E,$ as y,B as R,M as te,O as ne,a0 as oe}from"./.pnpm-Ddnih9Rt.js";import{i as $}from"./index-CdKk1Oin.js";import{_ as de}from"./_plugin-vue_export-helper-DlAUqK2U.js";const ue=s=>$.post("/admin/inv_code",s),se=s=>$.post("/admin/inv_code/generate",s),ie=s=>$.get(`/admin/inv_code?page=${s.page}&size=${s.size}`),pe=s=>$.patch(`/admin/inv_code/${s.id}`,s),re=s=>$.delete("/admin/inv_code",{data:{inv_code_ids:[s.id]}}),ce=s=>$.delete("/admin/inv_code",{data:{inv_code_ids:s}}),me=P({__name:"AddInvCode",props:{modelValue:{},modelModifiers:{}},emits:j(["getInvCodes"],["update:modelValue"]),setup(s,{emit:i}){const k=i,C=H(s,"modelValue"),V=v(!1),t=v({type:"single",group_id:0,name:"",can_count:10}),p=v(null),D={group_id:[{required:!0,message:"请输入邀请码用户组ID",trigger:"blur"}],name:[{required:!0,message:"请输入邀请码名称",trigger:"blur"}],can_count:[{required:!0,message:"请输入可注册次数",trigger:"blur"}],count:[{required:!0,message:"请输入邀请码个数",trigger:"blur"}]},F=async b=>{if(!(!b||!await b.validate()))try{V.value=!0,t.value.type==="single"?await ue({type:"single",name:t.value.name,can_count:t.value.can_count,group_id:t.value.group_id}):await se({type:"random",count:t.value.count,can_count:t.value.can_count,group_id:t.value.group_id}),x.success("添加成功")}finally{V.value=!1}},B=b=>{k("getInvCodes"),b()},q=()=>{C.value=!1,k("getInvCodes")};return(b,d)=>{const U=K,A=Q,n=h,u=G,f=W,r=ee,S=X,z=ae,L=Y;return g(),I(z,{title:"添加邀请码",width:"60%",modelValue:C.value,"onUpdate:modelValue":d[7]||(d[7]=o=>C.value=o),"before-close":B},{footer:l(()=>[a(S,{type:"info",onClick:d[5]||(d[5]=o=>q())},{default:l(()=>[_("取消")]),_:1}),a(S,{type:"primary",onClick:d[6]||(d[6]=o=>F(p.value))},{default:l(()=>[_("添加")]),_:1})]),default:l(()=>[m((g(),I(r,{ref_key:"addInvCodeFormRef",ref:p,model:t.value,rules:D,"label-width":"auto"},{default:l(()=>[a(n,{label:"创建方式",prop:"type"},{default:l(()=>[a(A,{modelValue:t.value.type,"onUpdate:modelValue":d[0]||(d[0]=o=>t.value.type=o)},{default:l(()=>[(g(),T(w,null,J(["single","random"],o=>a(U,{key:o,label:o,value:o},null,8,["label","value"])),64))]),_:1},8,["modelValue"])]),_:1}),a(n,{label:"可用次数",prop:"can_count"},{default:l(()=>[a(u,{modelValue:t.value.can_count,"onUpdate:modelValue":d[1]||(d[1]=o=>t.value.can_count=o)},null,8,["modelValue"])]),_:1}),a(n,{label:"用户组ID",prop:"group_id"},{default:l(()=>[a(u,{modelValue:t.value.group_id,"onUpdate:modelValue":d[2]||(d[2]=o=>t.value.group_id=o)},null,8,["modelValue"])]),_:1}),t.value.type==="single"?(g(),I(n,{key:0,label:"邀请码名称",prop:"name"},{default:l(()=>[a(f,{modelValue:t.value.name,"onUpdate:modelValue":d[3]||(d[3]=o=>t.value.name=o)},null,8,["modelValue"])]),_:1})):(g(),I(n,{key:1,label:"邀请码个数",prop:"count"},{default:l(()=>[a(u,{modelValue:t.value.count,"onUpdate:modelValue":d[4]||(d[4]=o=>t.value.count=o)},null,8,["modelValue"])]),_:1}))]),_:1},8,["model"])),[[L,V.value]])]),_:1},8,["modelValue"])}}}),_e=P({__name:"InvCodeManagement",setup(s){const i=v(!1),k=v(15),C=v(1),V=v(),t=v([]),p=async()=>{try{i.value=!0;const n=await ie({page:C.value,size:k.value});V.value=n.data}finally{i.value=!1}},D=async n=>{n.edit=!(n.edit??!1)},F=async n=>{n.edit=!(n.edit??!0),await B(n)},B=async n=>{try{i.value=!0,await pe(n),x.success("修改邀请码成功")}finally{i.value=!1,await p()}},q=async n=>{try{i.value=!0,await re(n),x.success("删除邀请码成功")}finally{i.value=!1,await p()}},b=async()=>{try{i.value=!0;const n=t.value.map(u=>u.id);await ce(n),x.success("删除邀请码成功")}finally{i.value=!1,await p()}},d=n=>t.value=n;le(p);const U=v(!1),A=()=>U.value=!U.value;return(n,u)=>{var N,O;const f=X,r=te,S=W,z=G,L=ne,o=oe,Z=Y;return g(),T(w,null,[a(me,{onGetInvCodes:p,modelValue:U.value,"onUpdate:modelValue":u[0]||(u[0]=e=>U.value=e)},null,8,["modelValue"]),a(f,{type:"primary",onClick:u[1]||(u[1]=e=>p())},{default:l(()=>[_("刷新列表")]),_:1}),a(f,{type:"primary",onClick:u[2]||(u[2]=e=>A())},{default:l(()=>[_("添加邀请码")]),_:1}),a(f,{type:"danger",disabled:t.value.length<=0,onClick:u[3]||(u[3]=e=>b())},{default:l(()=>[_(" 批量删除 ")]),_:1},8,["disabled"]),m((g(),I(L,{data:((N=V.value)==null?void 0:N.data)??[],border:"","show-overflow-tooltip":"",class:"table",onSelectionChange:d},{default:l(()=>[a(r,{type:"selection",width:"40"}),a(r,{prop:"id",label:"ID"}),a(r,{prop:"name",label:"邀请码名称"},{default:l(({row:e})=>[m(M("span",null,E(e.name),513),[[y,!e.edit]]),m(a(S,{modelValue:e.name,"onUpdate:modelValue":c=>e.name=c},null,8,["modelValue","onUpdate:modelValue"]),[[y,e.edit]])]),_:1}),a(r,{prop:"group_id",label:"用户组ID"},{default:l(({row:e})=>[m(M("span",null,E(e.group_id),513),[[y,!e.edit]]),m(a(z,{modelValue:e.group_id,"onUpdate:modelValue":c=>e.group_id=c},null,8,["modelValue","onUpdate:modelValue"]),[[y,e.edit]])]),_:1}),a(r,{prop:"can_count",label:"可用次数"},{default:l(({row:e})=>[m(M("span",null,E(e.can_count),513),[[y,!e.edit]]),m(a(z,{modelValue:e.can_count,"onUpdate:modelValue":c=>e.can_count=c},null,8,["modelValue","onUpdate:modelValue"]),[[y,e.edit]])]),_:1}),a(r,{prop:"use_count",label:"已使用次数"},{default:l(({row:e})=>[m(M("span",null,E(e.use_count),513),[[y,!e.edit]]),m(a(z,{modelValue:e.use_count,"onUpdate:modelValue":c=>e.use_count=c},null,8,["modelValue","onUpdate:modelValue"]),[[y,e.edit]])]),_:1}),a(r,{prop:"created_at",label:"创建时间"},{default:l(({row:e})=>[_(E(new Date(e.created_at).toLocaleString()),1)]),_:1}),a(r,{prop:"updated_at",label:"更新时间"},{default:l(({row:e})=>[_(E(new Date(e.updated_at).toLocaleString()),1)]),_:1}),a(r,{width:"150",label:"操作",fixed:"right"},{default:l(({row:e})=>[e.edit?R("",!0):(g(),I(f,{key:0,size:"small",type:"primary",onClick:c=>D(e)},{default:l(()=>[_(" 编辑 ")]),_:2},1032,["onClick"])),e.edit?(g(),I(f,{key:1,size:"small",type:"primary",onClick:c=>F(e)},{default:l(()=>[_(" 保存 ")]),_:2},1032,["onClick"])):R("",!0),a(f,{size:"small",type:"danger",onClick:c=>q(e)},{default:l(()=>[_("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])),[[Z,i.value]]),a(o,{"current-page":C.value,"onUpdate:currentPage":u[4]||(u[4]=e=>C.value=e),"page-size":k.value,"onUpdate:pageSize":u[5]||(u[5]=e=>k.value=e),"page-sizes":[15,30,50,100],total:((O=V.value)==null?void 0:O.total)??100,layout:"sizes, prev, pager, next",onSizeChange:p,onCurrentChange:p},null,8,["current-page","page-size","total"])],64)}}}),ye=de(_e,[["__scopeId","data-v-dbd3f6c9"]]);export{ye as default};
