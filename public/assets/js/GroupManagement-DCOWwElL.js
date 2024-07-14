import{i as _}from"./index-awDXPSQP.js";import{_ as S}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css              */const $=u=>_.post("/admin/group",u),E=u=>_.get(`/admin/group?page=${u.page}&size=${u.size}`),U=u=>_.patch(`/admin/group/${u.id}`,u),B=u=>_.delete("/admin/group",{data:{group_ids:[u.id]}}),M=u=>_.delete("/admin/group",{data:{group_ids:u}}),T=Vue.defineComponent({__name:"AddGroup",props:{modelValue:{},modelModifiers:{}},emits:Vue.mergeModels(["getGroups"],["update:modelValue"]),setup(u,{emit:r}){const v=r,c=Vue.useModel(u,"modelValue"),V=Vue.ref(!1),a=Vue.ref({name:"",count:0,size:0}),d=Vue.ref(null),C={name:[{required:!0,message:"请输入组名",trigger:"blur"}],count:[{required:!0,message:"请输入可解析文件个数",trigger:"blur"}],size:[{required:!0,message:"请输入可解析大小",trigger:"blur"}]},y=async m=>{if(!(!m||!await m.validate()))try{V.value=!0,await $(a.value),ElementPlus.ElMessage.success("添加成功")}finally{V.value=!1}},N=m=>{v("getGroups"),m()},x=()=>{c.value=!1,v("getGroups")};return(m,l)=>{const f=Vue.resolveComponent("el-input"),g=Vue.resolveComponent("el-form-item"),t=Vue.resolveComponent("el-input-number"),o=Vue.resolveComponent("el-form"),s=Vue.resolveComponent("el-button"),i=Vue.resolveComponent("el-dialog"),w=Vue.resolveDirective("loading");return Vue.openBlock(),Vue.createBlock(i,{title:"添加用户组",width:"60%",modelValue:c.value,"onUpdate:modelValue":l[5]||(l[5]=n=>c.value=n),"before-close":N},{footer:Vue.withCtx(()=>[Vue.createVNode(s,{type:"info",onClick:l[3]||(l[3]=n=>x())},{default:Vue.withCtx(()=>[Vue.createTextVNode("取消")]),_:1}),Vue.createVNode(s,{type:"primary",onClick:l[4]||(l[4]=n=>y(d.value))},{default:Vue.withCtx(()=>[Vue.createTextVNode("添加")]),_:1})]),default:Vue.withCtx(()=>[Vue.withDirectives((Vue.openBlock(),Vue.createBlock(o,{ref_key:"addGroupFormRef",ref:d,model:a.value,rules:C,"label-width":"auto"},{default:Vue.withCtx(()=>[Vue.createVNode(g,{label:"组名",prop:"name"},{default:Vue.withCtx(()=>[Vue.createVNode(f,{modelValue:a.value.name,"onUpdate:modelValue":l[0]||(l[0]=n=>a.value.name=n)},null,8,["modelValue"])]),_:1}),Vue.createVNode(g,{label:"可解析文件个数",prop:"count"},{default:Vue.withCtx(()=>[Vue.createVNode(t,{modelValue:a.value.count,"onUpdate:modelValue":l[1]||(l[1]=n=>a.value.count=n)},null,8,["modelValue"])]),_:1}),Vue.createVNode(g,{label:"可解析大小",prop:"size"},{default:Vue.withCtx(()=>[Vue.createVNode(t,{modelValue:a.value.size,"onUpdate:modelValue":l[2]||(l[2]=n=>a.value.size=n)},null,8,["modelValue"])]),_:1})]),_:1},8,["model"])),[[w,V.value]])]),_:1},8,["modelValue"])}}}),P=Vue.defineComponent({__name:"GroupManagement",setup(u){const r=Vue.ref(!1),v=Vue.ref(15),c=Vue.ref(1),V=Vue.ref(),a=Vue.ref([]),d=async()=>{try{r.value=!0;const t=await E({page:c.value,size:v.value});V.value=t.data}finally{r.value=!1}},C=async t=>{t.edit=!(t.edit??!1)},y=async t=>{t.edit=!(t.edit??!0),await N(t)},N=async t=>{try{r.value=!0,await U(t),ElementPlus.ElMessage.success("修改用户组成功")}finally{r.value=!1,await d()}},x=async t=>{try{r.value=!0,await B(t),ElementPlus.ElMessage.success("删除用户组成功")}finally{r.value=!1,await d()}},m=async()=>{try{r.value=!0;const t=a.value.map(o=>o.id);await M(t),ElementPlus.ElMessage.success("批量删除用户组成功")}finally{r.value=!1,await d()}},l=t=>a.value=t;Vue.onMounted(d);const f=Vue.ref(!1),g=()=>f.value=!f.value;return(t,o)=>{var b,G,h;const s=Vue.resolveComponent("el-button"),i=Vue.resolveComponent("el-table-column"),w=Vue.resolveComponent("el-input"),n=Vue.resolveComponent("el-input-number"),k=Vue.resolveComponent("el-table"),z=Vue.resolveComponent("el-pagination"),D=Vue.resolveDirective("loading");return Vue.openBlock(),Vue.createElementBlock(Vue.Fragment,null,[Vue.createVNode(T,{onGetGroups:d,modelValue:f.value,"onUpdate:modelValue":o[0]||(o[0]=e=>f.value=e)},null,8,["modelValue"]),Vue.createVNode(s,{type:"primary",onClick:o[1]||(o[1]=e=>d())},{default:Vue.withCtx(()=>[Vue.createTextVNode("刷新列表")]),_:1}),Vue.createVNode(s,{type:"primary",onClick:o[2]||(o[2]=e=>g())},{default:Vue.withCtx(()=>[Vue.createTextVNode("添加用户组")]),_:1}),Vue.createVNode(s,{type:"danger",disabled:a.value.length<=0,onClick:o[3]||(o[3]=e=>m())},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 批量删除 ")]),_:1},8,["disabled"]),Vue.withDirectives((Vue.openBlock(),Vue.createBlock(k,{data:((b=V.value)==null?void 0:b.data)??[],border:"","show-overflow-tooltip":"",class:"table",onSelectionChange:l},{default:Vue.withCtx(()=>[Vue.createVNode(i,{type:"selection",width:"40"}),Vue.createVNode(i,{prop:"id",label:"ID"}),Vue.createVNode(i,{prop:"name",label:"组名"},{default:Vue.withCtx(({row:e})=>[Vue.withDirectives(Vue.createElementVNode("span",null,Vue.toDisplayString(e.name),513),[[Vue.vShow,!e.edit]]),Vue.withDirectives(Vue.createVNode(w,{modelValue:e.name,"onUpdate:modelValue":p=>e.name=p},null,8,["modelValue","onUpdate:modelValue"]),[[Vue.vShow,e.edit]])]),_:1}),Vue.createVNode(i,{prop:"count",label:"可解析文件数量"},{default:Vue.withCtx(({row:e})=>[Vue.withDirectives(Vue.createElementVNode("span",null,Vue.toDisplayString(e.count),513),[[Vue.vShow,!e.edit]]),Vue.withDirectives(Vue.createVNode(n,{modelValue:e.count,"onUpdate:modelValue":p=>e.count=p},null,8,["modelValue","onUpdate:modelValue"]),[[Vue.vShow,e.edit]])]),_:1}),Vue.createVNode(i,{prop:"size",label:"可解析大小"},{default:Vue.withCtx(({row:e})=>[Vue.withDirectives(Vue.createElementVNode("span",null,Vue.toDisplayString(e.size),513),[[Vue.vShow,!e.edit]]),Vue.withDirectives(Vue.createVNode(n,{modelValue:e.size,"onUpdate:modelValue":p=>e.size=p},null,8,["modelValue","onUpdate:modelValue"]),[[Vue.vShow,e.edit]])]),_:1}),Vue.createVNode(i,{prop:"created_at",label:"创建时间"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(new Date(e.created_at).toLocaleString()),1)]),_:1}),Vue.createVNode(i,{prop:"updated_at",label:"更新时间"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(new Date(e.updated_at).toLocaleString()),1)]),_:1}),Vue.createVNode(i,{width:"150",label:"操作",fixed:"right"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createBlock(s,{key:0,size:"small",type:"primary",onClick:p=>C(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 编辑 ")]),_:2},1032,["onClick"])),e.edit?(Vue.openBlock(),Vue.createBlock(s,{key:1,size:"small",type:"primary",onClick:p=>y(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 保存 ")]),_:2},1032,["onClick"])):Vue.createCommentVNode("",!0),Vue.createVNode(s,{size:"small",type:"danger",onClick:p=>x(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])),[[D,r.value]]),Vue.createVNode(z,{"current-page":c.value,"onUpdate:currentPage":o[4]||(o[4]=e=>c.value=e),"page-size":v.value,"onUpdate:pageSize":o[5]||(o[5]=e=>v.value=e),"page-sizes":[15,50,100,500,((G=V.value)==null?void 0:G.total)??100],total:((h=V.value)==null?void 0:h.total)??100,layout:"total, sizes, prev, pager, next, jumper",onSizeChange:d,onCurrentChange:d},null,8,["current-page","page-size","page-sizes","total"])],64)}}}),L=S(P,[["__scopeId","data-v-6ac724f9"]]);export{L as default};
