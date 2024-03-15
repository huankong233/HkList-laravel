import{C as le,I as R,U as A,t as pe,d as X}from"./base-BmRrU_aT.js";import{v as fe}from"./el-loading-CTgX8ugY.js";import{E as ge,a as be}from"./el-form-item-BABl4hZ5.js";import{E as Ve}from"./el-button-z8-J4eJW.js";import{E as re}from"./el-input-BS417mK1.js";import{E as ve}from"./el-switch-DMxvBT2i.js";import{b as we,c as Ne,e as Ee}from"./admin-CQULZZHD.js";import{a7 as Z,a as j,r as H,N as _e,g as k,G as he,o as ae,J as ye,e as I,c as K,P as J,u as n,n as $,af as z,b as o,w as f,R as F,i as ee,a6 as W,L as Ie,d as Se,k as Ce,ag as Pe,q as xe,B as ke}from"./index-Dk6oAfKH.js";import{b as Ae,y as Fe,n as h,C as U,D as Ue,u as Te,l as O,F as Me,G as Be,f as ne,H as De,p as ze,_ as Oe,w as Re,E as Y}from"./request-FGLSB9uO.js";import{a as qe,u as Le,b as Ge}from"./use-form-item-Da74mal4.js";import"./_initCloneObject-DSE19YjO.js";const Ke=100,$e=600,te={beforeMount(i,E){const t=E.value,{interval:a=Ke,delay:C=$e}=Z(t)?{}:t;let b,v;const d=()=>Z(t)?t():t.handler(),N=()=>{v&&(clearTimeout(v),v=void 0),b&&(clearInterval(b),b=void 0)};i.addEventListener("mousedown",V=>{V.button===0&&(N(),d(),document.addEventListener("mouseup",()=>N(),{once:!0}),v=setTimeout(()=>{b=setInterval(()=>{d()},a)},C))})}},We=Ae({id:{type:String,default:void 0},step:{type:Number,default:1},stepStrictly:Boolean,max:{type:Number,default:Number.POSITIVE_INFINITY},min:{type:Number,default:Number.NEGATIVE_INFINITY},modelValue:Number,readonly:Boolean,disabled:Boolean,size:Fe,controls:{type:Boolean,default:!0},controlsPosition:{type:String,default:"",values:["","right"]},valueOnClear:{type:[String,Number,null],validator:i=>i===null||h(i)||["min","max"].includes(i),default:null},name:String,label:String,placeholder:String,precision:{type:Number,validator:i=>i>=0&&i===Number.parseInt(`${i}`,10)},validateEvent:{type:Boolean,default:!0}}),Ye={[le]:(i,E)=>E!==i,blur:i=>i instanceof FocusEvent,focus:i=>i instanceof FocusEvent,[R]:i=>h(i)||U(i),[A]:i=>h(i)||U(i)},He=["aria-label","onKeydown"],Je=["aria-label","onKeydown"],je=j({name:"ElInputNumber"}),Qe=j({...je,props:We,emits:Ye,setup(i,{expose:E,emit:t}){const a=i,{t:C}=Ue(),b=Te("input-number"),v=H(),d=_e({currentValue:a.modelValue,userInput:null}),{formItem:N}=qe(),V=k(()=>h(a.modelValue)&&a.modelValue<=a.min),r=k(()=>h(a.modelValue)&&a.modelValue>=a.max),g=k(()=>{const e=T(a.step);return O(a.precision)?Math.max(T(a.modelValue),e):(e>a.precision,a.precision)}),m=k(()=>a.controls&&a.controlsPosition==="right"),S=Le(),y=Ge(),P=k(()=>{if(d.userInput!==null)return d.userInput;let e=d.currentValue;if(U(e))return"";if(h(e)){if(Number.isNaN(e))return"";O(a.precision)||(e=e.toFixed(a.precision))}return e}),x=(e,l)=>{if(O(l)&&(l=g.value),l===0)return Math.round(e);let s=String(e);const c=s.indexOf(".");if(c===-1||!s.replace(".","").split("")[c+l])return e;const B=s.length;return s.charAt(B-1)==="5"&&(s=`${s.slice(0,Math.max(0,B-1))}6`),Number.parseFloat(Number(s).toFixed(l))},T=e=>{if(U(e))return 0;const l=e.toString(),s=l.indexOf(".");let c=0;return s!==-1&&(c=l.length-s-1),c},u=(e,l=1)=>h(e)?x(e+a.step*l):d.currentValue,q=()=>{if(a.readonly||y.value||r.value)return;const e=Number(P.value)||0,l=u(e);M(l),t(R,d.currentValue),G()},L=()=>{if(a.readonly||y.value||V.value)return;const e=Number(P.value)||0,l=u(e,-1);M(l),t(R,d.currentValue),G()},Q=(e,l)=>{const{max:s,min:c,step:p,precision:_,stepStrictly:B,valueOnClear:D}=a;s<c&&pe("InputNumber","min should not be greater than max.");let w=Number(e);if(U(e)||Number.isNaN(w))return null;if(e===""){if(D===null)return null;w=Ie(D)?{min:c,max:s}[D]:D}return B&&(w=x(Math.round(w/p)*p,_)),O(_)||(w=x(w,_)),(w>s||w<c)&&(w=w>s?s:c,l&&t(A,w)),w},M=(e,l=!0)=>{var s;const c=d.currentValue,p=Q(e);if(!l){t(A,p);return}c===p&&e||(d.userInput=null,t(A,p),c!==p&&t(le,p,c),a.validateEvent&&((s=N==null?void 0:N.validate)==null||s.call(N,"change").catch(_=>X())),d.currentValue=p)},oe=e=>{d.userInput=e;const l=e===""?null:Number(e);t(R,l),M(l,!1)},ue=e=>{const l=e!==""?Number(e):"";(h(l)&&!Number.isNaN(l)||e==="")&&M(l),G(),d.userInput=null},se=()=>{var e,l;(l=(e=v.value)==null?void 0:e.focus)==null||l.call(e)},ie=()=>{var e,l;(l=(e=v.value)==null?void 0:e.blur)==null||l.call(e)},de=e=>{t("focus",e)},me=e=>{var l;d.userInput=null,t("blur",e),a.validateEvent&&((l=N==null?void 0:N.validate)==null||l.call(N,"blur").catch(s=>X()))},G=()=>{d.currentValue!==a.modelValue&&(d.currentValue=a.modelValue)},ce=e=>{document.activeElement===e.target&&e.preventDefault()};return he(()=>a.modelValue,(e,l)=>{const s=Q(e,!0);d.userInput===null&&s!==l&&(d.currentValue=s)},{immediate:!0}),ae(()=>{var e;const{min:l,max:s,modelValue:c}=a,p=(e=v.value)==null?void 0:e.input;if(p.setAttribute("role","spinbutton"),Number.isFinite(s)?p.setAttribute("aria-valuemax",String(s)):p.removeAttribute("aria-valuemax"),Number.isFinite(l)?p.setAttribute("aria-valuemin",String(l)):p.removeAttribute("aria-valuemin"),p.setAttribute("aria-valuenow",d.currentValue||d.currentValue===0?String(d.currentValue):""),p.setAttribute("aria-disabled",String(y.value)),!h(c)&&c!=null){let _=Number(c);Number.isNaN(_)&&(_=null),t(A,_)}}),ye(()=>{var e,l;const s=(e=v.value)==null?void 0:e.input;s==null||s.setAttribute("aria-valuenow",`${(l=d.currentValue)!=null?l:""}`)}),E({focus:se,blur:ie}),(e,l)=>(I(),K("div",{class:$([n(b).b(),n(b).m(n(S)),n(b).is("disabled",n(y)),n(b).is("without-controls",!e.controls),n(b).is("controls-right",n(m))]),onDragstart:l[0]||(l[0]=W(()=>{},["prevent"]))},[e.controls?J((I(),K("span",{key:0,role:"button","aria-label":n(C)("el.inputNumber.decrease"),class:$([n(b).e("decrease"),n(b).is("disabled",n(V))]),onKeydown:z(L,["enter"])},[o(n(ne),null,{default:f(()=>[n(m)?(I(),F(n(Me),{key:0})):(I(),F(n(Be),{key:1}))]),_:1})],42,He)),[[n(te),L]]):ee("v-if",!0),e.controls?J((I(),K("span",{key:1,role:"button","aria-label":n(C)("el.inputNumber.increase"),class:$([n(b).e("increase"),n(b).is("disabled",n(r))]),onKeydown:z(q,["enter"])},[o(n(ne),null,{default:f(()=>[n(m)?(I(),F(n(De),{key:0})):(I(),F(n(ze),{key:1}))]),_:1})],42,Je)),[[n(te),q]]):ee("v-if",!0),o(n(re),{id:e.id,ref_key:"input",ref:v,type:"number",step:e.step,"model-value":n(P),placeholder:e.placeholder,readonly:e.readonly,disabled:n(y),size:n(S),max:e.max,min:e.min,name:e.name,label:e.label,"validate-event":!1,onWheel:ce,onKeydown:[z(W(q,["prevent"]),["up"]),z(W(L,["prevent"]),["down"])],onBlur:me,onFocus:de,onInput:oe,onChange:ue},null,8,["id","step","model-value","placeholder","readonly","disabled","size","max","min","name","label","onKeydown"])],34))}});var Xe=Oe(Qe,[["__file","input-number.vue"]]);const Ze=Re(Xe),en=Se("ChangeConfig",()=>{const i=H({version:"",frontEndVersion:"",userAgent:"",sleep:1,maxOnce:20,announceSwitch:!1,announce:"",cookie:"",ssl:!1,prefix:"/admin",debug:!1,password:"",passwordSwitch:!1,getPending:!1,changePending:!1}),E=H(null);return{changeConfigForm:i,changeConfigFormRef:E}}),pn=j({__name:"ChangeConfig",setup(i){const E=en(),{changeConfigForm:t,changeConfigFormRef:a}=Ce(E),v={userAgent:[{required:!0,message:"请输入User_Agent",trigger:"blur"}],announce:[{validator:(V,r,g)=>{t.value.passwordSwitch&&r===""&&g(new Error("请输入公告内容")),g()},trigger:"blur"}],announceSwitch:[{required:!0,message:"请确认开关状态",trigger:"blur"}],debug:[{required:!0,message:"请确认开关状态",trigger:"blur"}],ssl:[{required:!0,message:"请确认开关状态",trigger:"blur"}],prefix:[{required:!0,message:"请输入后台接口后缀",trigger:"blur"}],cookie:[{required:!0,message:"请输入获取列表时的 Cookie",trigger:"blur"}],maxOnce:[{required:!0,message:"请输入批量解析时单次最大解析数量",trigger:"blur"}],sleep:[{required:!0,message:"请输入批量解析时休眠时间(秒)",trigger:"blur"}],password:[{validator:(V,r,g)=>{t.value.passwordSwitch&&r===""&&g(new Error("请输入密码")),g()},trigger:"blur"}],passwordSwitch:[{required:!0,message:"请确认开关状态",trigger:"blur"}]};ae(async()=>await d());const d=async()=>{t.value.getPending=!0;const V=await we()??"failed";t.value.getPending=!1,V.toString()!=="failed"&&(t.value={...t.value,...V.data,frontEndVersion:await Pe()})},N=async V=>{if(V&&await V.validate(()=>{})){t.value.changePending=!0;const r=await Ne({cookie:t.value.cookie,check:!0})??"failed";if(r.toString()==="failed")return;if(t.value.changePending=!1,r.message!=="cookie校验成功"){Y.error("请不要使用包含账户的cookie,直接退出登陆获取一个新cookie");return}Y.success("cookie校验成功,不包含账户");const g=await Ee({...t.value})??"failed";if(t.value.changePending=!1,g.toString()==="failed")return;Y.success("修改成功"),ke(t.value.prefix),await d()}};return(V,r)=>{const g=re,m=ge,S=ve,y=Ze,P=Ve,x=be,T=fe;return J((I(),F(x,{ref_key:"changeConfigFormRef",ref:a,model:n(t),rules:v,"label-width":"auto"},{default:f(()=>[o(m,{label:"后端版本号",prop:"version"},{default:f(()=>[o(g,{disabled:"",modelValue:n(t).version,"onUpdate:modelValue":r[0]||(r[0]=u=>n(t).version=u)},null,8,["modelValue"])]),_:1}),o(m,{label:"前端版本号",prop:"frontEndVersion"},{default:f(()=>[o(g,{disabled:"",modelValue:n(t).frontEndVersion,"onUpdate:modelValue":r[1]||(r[1]=u=>n(t).frontEndVersion=u)},null,8,["modelValue"])]),_:1}),o(m,{label:"DEBUG模式开关",prop:"debug"},{default:f(()=>[o(S,{modelValue:n(t).debug,"onUpdate:modelValue":r[2]||(r[2]=u=>n(t).debug=u),size:"large"},null,8,["modelValue"])]),_:1}),o(m,{label:"强制SSL模式开关",prop:"ssl"},{default:f(()=>[o(S,{modelValue:n(t).ssl,"onUpdate:modelValue":r[3]||(r[3]=u=>n(t).ssl=u),size:"large"},null,8,["modelValue"])]),_:1}),o(m,{label:"公告开关",prop:"announceSwitch"},{default:f(()=>[o(S,{modelValue:n(t).announceSwitch,"onUpdate:modelValue":r[4]||(r[4]=u=>n(t).announceSwitch=u),size:"large"},null,8,["modelValue"])]),_:1}),o(m,{label:"公告内容",prop:"announce"},{default:f(()=>[o(g,{type:"textarea",modelValue:n(t).announce,"onUpdate:modelValue":r[5]||(r[5]=u=>n(t).announce=u),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1}),o(m,{label:"后台接口前缀",prop:"prefix"},{default:f(()=>[o(g,{modelValue:n(t).prefix,"onUpdate:modelValue":r[6]||(r[6]=u=>n(t).prefix=u),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1}),o(m,{label:"下载使用的 User_Agent",prop:"userAgent"},{default:f(()=>[o(g,{modelValue:n(t).userAgent,"onUpdate:modelValue":r[7]||(r[7]=u=>n(t).userAgent=u),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1}),o(m,{label:"批量解析时休眠时间(秒)",prop:"sleep"},{default:f(()=>[o(y,{modelValue:n(t).sleep,"onUpdate:modelValue":r[8]||(r[8]=u=>n(t).sleep=u)},null,8,["modelValue"])]),_:1}),o(m,{label:"批量解析时单次最大解析数量",prop:"maxOnce"},{default:f(()=>[o(y,{modelValue:n(t).maxOnce,"onUpdate:modelValue":r[9]||(r[9]=u=>n(t).maxOnce=u)},null,8,["modelValue"])]),_:1}),o(m,{label:"获取列表时的 Cookie",prop:"cookie"},{default:f(()=>[o(g,{type:"textarea",modelValue:n(t).cookie,"onUpdate:modelValue":r[10]||(r[10]=u=>n(t).cookie=u),modelModifiers:{trim:!0},rows:"5"},null,8,["modelValue"])]),_:1}),o(m,{label:"密码开关",prop:"passwordSwitch"},{default:f(()=>[o(S,{modelValue:n(t).passwordSwitch,"onUpdate:modelValue":r[11]||(r[11]=u=>n(t).passwordSwitch=u),size:"large"},null,8,["modelValue"])]),_:1}),o(m,{label:"密码",prop:"password"},{default:f(()=>[o(g,{modelValue:n(t).password,"onUpdate:modelValue":r[12]||(r[12]=u=>n(t).password=u),modelModifiers:{trim:!0}},null,8,["modelValue"])]),_:1}),o(m,{label:" "},{default:f(()=>[o(P,{type:"primary",onClick:r[13]||(r[13]=u=>N(n(a))),loading:n(t).changePending},{default:f(()=>[xe(" 保存 ")]),_:1},8,["loading"])]),_:1})]),_:1},8,["model"])),[[T,n(t).getPending]])}}});export{pn as default};
