import{u as p}from"./aria2Store-DWFJrwUO.js";import"./index-awDXPSQP.js";/* empty css              */const w=Vue.defineComponent({__name:"Aria2Dialog",setup(f){const n=p(),{aria2ConfigForm:t,aria2ConfigFormRef:a,aria2ConfigDialogVisible:l}=Pinia.storeToRefs(n),i={host:[{required:!0,validator:(V,e,r)=>e===""?r(new Error("请输入Aria2 服务器地址")):e.includes("jsonrpc")?r(new Error("地址不需要包含端口或jsonrpc路径")):r(),message:"",trigger:"blur"}],port:[{required:!0,message:"请输入Aria2 端口号",trigger:"blur"}]};return(V,e)=>{const r=Vue.resolveComponent("el-input"),u=Vue.resolveComponent("el-form-item"),d=Vue.resolveComponent("el-button"),m=Vue.resolveComponent("el-form"),s=Vue.resolveComponent("el-dialog");return Vue.openBlock(),Vue.createBlock(s,{modelValue:Vue.unref(l),"onUpdate:modelValue":e[4]||(e[4]=o=>Vue.isRef(l)?l.value=o:null),title:"Aria2配置",width:"90%"},{default:Vue.withCtx(()=>[Vue.createVNode(m,{ref_key:"aria2ConfigFormRef",ref:a,model:Vue.unref(t),rules:i,"label-width":"auto"},{default:Vue.withCtx(()=>[Vue.createVNode(u,{label:"Aria2 服务器地址",prop:"host"},{default:Vue.withCtx(()=>[Vue.createVNode(r,{modelValue:Vue.unref(t).host,"onUpdate:modelValue":e[0]||(e[0]=o=>Vue.unref(t).host=o)},null,8,["modelValue"])]),_:1}),Vue.createVNode(u,{label:"Aria2 端口号",prop:"port"},{default:Vue.withCtx(()=>[Vue.createVNode(r,{modelValue:Vue.unref(t).port,"onUpdate:modelValue":e[1]||(e[1]=o=>Vue.unref(t).port=o)},null,8,["modelValue"])]),_:1}),Vue.createVNode(u,{label:"Aria2 下载密钥",prop:"token"},{default:Vue.withCtx(()=>[Vue.createVNode(r,{modelValue:Vue.unref(t).token,"onUpdate:modelValue":e[2]||(e[2]=o=>Vue.unref(t).token=o)},null,8,["modelValue"])]),_:1}),Vue.createVNode(u,{label:" "},{default:Vue.withCtx(()=>[Vue.createVNode(d,{type:"primary",onClick:e[3]||(e[3]=o=>Vue.unref(n).saveAria2Config())},{default:Vue.withCtx(()=>[Vue.createTextVNode(" 保存 ")]),_:1})]),_:1})]),_:1},8,["model"])]),_:1},8,["modelValue"])}}});export{w as default};
