import{i as m}from"./index-DVOE1D94.js";import{f as E}from"./format-DaCfCw6f.js";import{_ as M}from"./_plugin-vue_export-helper-DlAUqK2U.js";const P=u=>m.post("/admin/account",u),L=u=>m.get(`/admin/account?page=${u.page}&size=${u.size}`),F=u=>m.patch(`/admin/account/${u.id}`,u),I=u=>m.patch("/admin/account/info",{account_ids:[u.id]}),R=u=>m.patch("/admin/account/info",{account_ids:u}),j=u=>m.delete("/admin/account",{data:{account_ids:[u.id]}}),q=u=>m.delete("/admin/account",{data:{account_ids:u}}),D=u=>m.patch("/admin/account/switch",u),G=Vue.defineComponent({__name:"AddAccount",props:{modelValue:{},modelModifiers:{}},emits:Vue.mergeModels(["getAccounts"],["update:modelValue"]),setup(u,{emit:n}){const y=n,_=Vue.useModel(u,"modelValue"),p=Vue.ref(!1),c=Vue.ref({type:1,cookie:""}),d=Vue.ref(null),x={cookie:[{required:!0,message:"请输入账户信息",trigger:"blur"}]},g=async k=>{if(!(!k||!await k.validate()))try{p.value=!0,(await P(c.value)).data.have_repeat&&ElementPlus.ElMessage.info("存在重复的账号,已自动过滤"),ElementPlus.ElMessage.success("添加成功")}finally{p.value=!1}},N=k=>{y("getAccounts"),k()},h=()=>{_.value=!1,y("getAccounts")};return(k,V)=>{const B=Vue.resolveComponent("el-text"),r=Vue.resolveComponent("el-form-item"),v=Vue.resolveComponent("el-option"),C=Vue.resolveComponent("el-select"),a=Vue.resolveComponent("el-input"),o=Vue.resolveComponent("el-form"),s=Vue.resolveComponent("el-button"),l=Vue.resolveComponent("el-dialog"),f=Vue.resolveDirective("loading");return Vue.openBlock(),Vue.createBlock(l,{title:"添加账号",width:"60%",modelValue:_.value,"onUpdate:modelValue":V[5]||(V[5]=i=>_.value=i),"before-close":N},{footer:Vue.withCtx(()=>[Vue.createVNode(s,{type:"info",onClick:V[3]||(V[3]=i=>h())},{default:Vue.withCtx(()=>[Vue.createTextVNode("取消")]),_:1}),Vue.createVNode(s,{type:"primary",onClick:V[4]||(V[4]=i=>g(d.value))},{default:Vue.withCtx(()=>[Vue.createTextVNode("添加")]),_:1})]),default:Vue.withCtx(()=>[Vue.withDirectives((Vue.openBlock(),Vue.createBlock(o,{ref_key:"addAccountFormRef",ref:d,model:c.value,rules:x,"label-width":"auto"},{default:Vue.withCtx(()=>[Vue.createVNode(r,{label:"提示"},{default:Vue.withCtx(()=>[Vue.createVNode(B,null,{default:Vue.withCtx(()=>[Vue.createTextVNode("可以使用换行来分割多个账号")]),_:1})]),_:1}),Vue.createVNode(r,{label:"账号类型"},{default:Vue.withCtx(()=>[Vue.createVNode(C,{modelValue:c.value.type,"onUpdate:modelValue":V[0]||(V[0]=i=>c.value.type=i)},{default:Vue.withCtx(()=>[Vue.createVNode(v,{label:"cookie",value:1}),Vue.createVNode(v,{label:"token",value:2})]),_:1},8,["modelValue"])]),_:1}),c.value.type===1?(Vue.openBlock(),Vue.createBlock(r,{key:0,label:"Cookie",prop:"cookie"},{default:Vue.withCtx(()=>[Vue.createVNode(a,{type:"textarea",modelValue:c.value.cookie,"onUpdate:modelValue":V[1]||(V[1]=i=>c.value.cookie=i)},null,8,["modelValue"])]),_:1})):(Vue.openBlock(),Vue.createBlock(r,{key:1,label:"refresh_token",prop:"cookie"},{default:Vue.withCtx(()=>[Vue.createVNode(a,{type:"textarea",modelValue:c.value.cookie,"onUpdate:modelValue":V[2]||(V[2]=i=>c.value.cookie=i)},null,8,["modelValue"])]),_:1}))]),_:1},8,["model"])),[[f,p.value]])]),_:1},8,["modelValue"])}}}),H={key:0},J={key:0},K={key:0},O={key:0},Q={key:0},W={key:0},X={key:0},Y={key:0},Z={key:0},ee=Vue.defineComponent({__name:"AccountManagement",setup(u){const n=Vue.ref(!1),y=Vue.ref(15),_=Vue.ref(1),p=Vue.ref(),c=Vue.ref([]),d=async()=>{try{n.value=!0;const a=await L({page:_.value,size:y.value});a.data.data=a.data.data.map(o=>(o.switch=!!o.switch,o)),p.value=a.data}finally{n.value=!1}},x=async a=>{try{n.value=!0,await I(a),ElementPlus.ElMessage.success("更新账户信息成功")}finally{n.value=!1,await d()}},g=async()=>{try{n.value=!0;const a=c.value.map(o=>o.id);await R(a),ElementPlus.ElMessage.success("批量更新账户成功")}finally{n.value=!1,await d()}},N=async a=>{try{n.value=!0,await j(a),ElementPlus.ElMessage.success("删除账户成功")}finally{n.value=!1,await d()}},h=async()=>{try{n.value=!0;const a=c.value.map(o=>o.id);await q(a),ElementPlus.ElMessage.success("批量删除账户成功")}finally{n.value=!1,await d()}},k=async()=>{try{n.value=!0;const a=c.value.map(o=>o.id);await D({account_ids:a,switch:1}),ElementPlus.ElMessage.success("批量启用账户成功")}finally{n.value=!1,await d()}},V=async()=>{try{n.value=!0;const a=c.value.map(o=>o.id);await D({account_ids:a,switch:0}),ElementPlus.ElMessage.success("批量禁用账户成功")}finally{n.value=!1,await d()}},B=a=>c.value=a;Vue.onMounted(d);const r=Vue.ref(!1),v=()=>r.value=!r.value,C=async a=>{if(a.edit=!a.edit,a.edit===!1)try{n.value=!0,await F(a),ElementPlus.ElMessage.success("修改賬號成功")}finally{n.value=!1,await d()}};return(a,o)=>{var b,A,S;const s=Vue.resolveComponent("el-button"),l=Vue.resolveComponent("el-table-column"),f=Vue.resolveComponent("el-input"),i=Vue.resolveComponent("el-option"),w=Vue.resolveComponent("el-select"),U=Vue.resolveComponent("el-switch"),T=Vue.resolveComponent("el-table"),z=Vue.resolveComponent("el-pagination"),$=Vue.resolveDirective("loading");return Vue.openBlock(),Vue.createElementBlock(Vue.Fragment,null,[Vue.createVNode(G,{onGetAccounts:d,modelValue:r.value,"onUpdate:modelValue":o[0]||(o[0]=e=>r.value=e)},null,8,["modelValue"]),Vue.createVNode(s,{type:"primary",onClick:o[1]||(o[1]=e=>d())},{default:Vue.withCtx(()=>[Vue.createTextVNode("刷新列表")]),_:1}),Vue.createVNode(s,{type:"primary",onClick:o[2]||(o[2]=e=>v())},{default:Vue.withCtx(()=>[Vue.createTextVNode("添加账号")]),_:1}),Vue.createVNode(s,{type:"primary",disabled:c.value.length<=0,onClick:o[3]||(o[3]=e=>g())},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 批量更新信息 ")]),_:1},8,["disabled"]),Vue.createVNode(s,{type:"primary",disabled:c.value.length<=0,onClick:o[4]||(o[4]=e=>k())},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 批量启用 ")]),_:1},8,["disabled"]),Vue.createVNode(s,{type:"primary",disabled:c.value.length<=0,onClick:o[5]||(o[5]=e=>V())},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 批量禁用 ")]),_:1},8,["disabled"]),Vue.createVNode(s,{type:"danger",disabled:c.value.length<=0,onClick:o[6]||(o[6]=e=>h())},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 批量删除 ")]),_:1},8,["disabled"]),Vue.withDirectives((Vue.openBlock(),Vue.createBlock(T,{data:((b=p.value)==null?void 0:b.data)??[],border:"","show-overflow-tooltip":"",class:"table",onSelectionChange:B},{default:Vue.withCtx(()=>[Vue.createVNode(l,{type:"selection",fixed:"",width:"40"}),Vue.createVNode(l,{prop:"id",label:"ID",fixed:""}),Vue.createVNode(l,{prop:"baidu_name",label:"百度用户名",width:"150px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",H,Vue.toDisplayString(e.baidu_name),1)),e.edit?(Vue.openBlock(),Vue.createBlock(f,{key:1,modelValue:e.baidu_name,"onUpdate:modelValue":t=>e.baidu_name=t},null,8,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"today_size",label:"今日解析",width:"150px"},{default:Vue.withCtx(({row:e})=>[Vue.createElementVNode("span",null,Vue.toDisplayString(e.today_count)+" ("+Vue.toDisplayString(Vue.unref(E)(e.today_size??0))+")",1)]),_:1}),Vue.createVNode(l,{prop:"today_size",label:"总共解析",width:"150px"},{default:Vue.withCtx(({row:e})=>[Vue.createElementVNode("span",null,Vue.toDisplayString(e.total_count)+" ("+Vue.toDisplayString(Vue.unref(E)(e.total_size??0))+")",1)]),_:1}),Vue.createVNode(l,{prop:"account_type",label:"账号类型",width:"160px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",J,Vue.toDisplayString(e.account_type),1)),e.edit?(Vue.openBlock(),Vue.createBlock(w,{key:1,modelValue:e.account_type,"onUpdate:modelValue":t=>e.account_type=t},{default:Vue.withCtx(()=>[(Vue.openBlock(),Vue.createElementBlock(Vue.Fragment,null,Vue.renderList(["cookie","access_token"],t=>Vue.createVNode(i,{key:t,value:t},{default:Vue.withCtx(()=>[Vue.createTextVNode(Vue.toDisplayString(t),1)]),_:2},1032,["value"])),64))]),_:2},1032,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"cookie",label:"Cookie",width:"150px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",K,Vue.toDisplayString(e.cookie),1)),e.edit?(Vue.openBlock(),Vue.createBlock(f,{key:1,modelValue:e.cookie,"onUpdate:modelValue":t=>e.cookie=t},null,8,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"access_token",label:"access_token",width:"150px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",O,Vue.toDisplayString(e.access_token),1)),e.edit?(Vue.openBlock(),Vue.createBlock(f,{key:1,modelValue:e.access_token,"onUpdate:modelValue":t=>e.access_token=t},null,8,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"refresh_token",label:"refresh_token",width:"150px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",Q,Vue.toDisplayString(e.refresh_token),1)),e.edit?(Vue.openBlock(),Vue.createBlock(f,{key:1,modelValue:e.refresh_token,"onUpdate:modelValue":t=>e.refresh_token=t},null,8,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"expired_at",label:"token过期时间",width:"160px"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(e.expired_at?new Date(e.expired_at).toLocaleString():"非token模式"),1)]),_:1}),Vue.createVNode(l,{prop:"vip_type",label:"会员类型",width:"130px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",W,Vue.toDisplayString(e.vip_type),1)),e.edit?(Vue.openBlock(),Vue.createBlock(w,{key:1,modelValue:e.vip_type,"onUpdate:modelValue":t=>e.vip_type=t},{default:Vue.withCtx(()=>[(Vue.openBlock(),Vue.createElementBlock(Vue.Fragment,null,Vue.renderList(["超级会员","假超级会员","普通会员","普通用户"],t=>Vue.createVNode(i,{key:t,value:t},{default:Vue.withCtx(()=>[Vue.createTextVNode(Vue.toDisplayString(t),1)]),_:2},1032,["value"])),64))]),_:2},1032,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"switch",label:"状态",width:"70px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",X,Vue.toDisplayString(e.switch?"启用":"禁用"),1)),e.edit?(Vue.openBlock(),Vue.createBlock(U,{key:1,modelValue:e.switch,"onUpdate:modelValue":t=>e.switch=t},null,8,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"prov",label:"省份",width:"120px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",Y,Vue.toDisplayString(e.prov??"未使用"),1)),e.edit?(Vue.openBlock(),Vue.createBlock(w,{key:1,modelValue:e.prov,"onUpdate:modelValue":t=>e.prov=t},{default:Vue.withCtx(()=>[(Vue.openBlock(),Vue.createBlock(i,{key:null,value:null},{default:Vue.withCtx(()=>[Vue.createTextVNode("未使用")]),_:1})),(Vue.openBlock(),Vue.createElementBlock(Vue.Fragment,null,Vue.renderList(["北京市","天津市","上海市","重庆市","河北省","山西省","内蒙古自治区","辽宁省","吉林省","黑龙江省","江苏省","浙江省","安徽省","福建省","江西省","山东省","河南省","湖北省","湖南省","广东省","广西壮族自治区","海南省","四川省","贵州省","云南省","西藏自治区","陕西省","甘肃省","青海省","宁夏回族自治区","新疆维吾尔自治区","香港特别行政区","澳门特别行政区","台湾省"],t=>Vue.createVNode(i,{key:t,label:t,value:t},null,8,["label","value"])),64))]),_:2},1032,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"reason",label:"禁用原因",width:"150px"},{default:Vue.withCtx(({row:e})=>[e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createElementBlock("span",Z,Vue.toDisplayString(e.reason??"未禁用"),1)),e.edit?(Vue.openBlock(),Vue.createBlock(f,{key:1,modelValue:e.reason,"onUpdate:modelValue":t=>e.reason=t},null,8,["modelValue","onUpdate:modelValue"])):Vue.createCommentVNode("",!0)]),_:1}),Vue.createVNode(l,{prop:"svip_end_at",label:"超级会员结束时间",width:"160px"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(new Date(e.svip_end_at).toLocaleString()),1)]),_:1}),Vue.createVNode(l,{prop:"last_use_at",label:"上次使用时间",width:"160px"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(new Date(e.last_use_at).toLocaleString()),1)]),_:1}),Vue.createVNode(l,{prop:"created_at",label:"创建时间",width:"160px"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(new Date(e.created_at).toLocaleString()),1)]),_:1}),Vue.createVNode(l,{prop:"updated_at",label:"更新时间",width:"160px"},{default:Vue.withCtx(({row:e})=>[Vue.createTextVNode(Vue.toDisplayString(new Date(e.updated_at).toLocaleString()),1)]),_:1}),Vue.createVNode(l,{width:"220",label:"操作",fixed:"right"},{default:Vue.withCtx(({row:e})=>[Vue.createVNode(s,{size:"small",type:"primary",onClick:t=>x(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode("更新信息")]),_:2},1032,["onClick"]),e.edit?Vue.createCommentVNode("",!0):(Vue.openBlock(),Vue.createBlock(s,{key:0,size:"small",type:"primary",onClick:t=>C(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 編輯 ")]),_:2},1032,["onClick"])),e.edit?(Vue.openBlock(),Vue.createBlock(s,{key:1,size:"small",type:"primary",onClick:t=>C(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 完成 ")]),_:2},1032,["onClick"])):Vue.createCommentVNode("",!0),Vue.createVNode(s,{size:"small",type:"danger",onClick:t=>N(e)},{default:Vue.withCtx(()=>[Vue.createTextVNode("删除")]),_:2},1032,["onClick"])]),_:1})]),_:1},8,["data"])),[[$,n.value]]),Vue.createVNode(z,{"current-page":_.value,"onUpdate:currentPage":o[7]||(o[7]=e=>_.value=e),"page-size":y.value,"onUpdate:pageSize":o[8]||(o[8]=e=>y.value=e),"page-sizes":[15,50,100,500,((A=p.value)==null?void 0:A.total)??100],total:((S=p.value)==null?void 0:S.total)??100,layout:"total, sizes, prev, pager, next, jumper",onSizeChange:d,onCurrentChange:d},null,8,["current-page","page-size","page-sizes","total"])],64)}}}),le=M(ee,[["__scopeId","data-v-0ce37547"]]);export{le as default};
