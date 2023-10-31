import{a7 as de,o as te,T as fe,a9 as me,a1 as ve,ah as pe,G as W,W as ye,a0 as K,a as Y,b as R,h,aa as ge,E as J,g as D,e as P,c as U,p as H,n as k,u as o,z as Ce,w as T,R as X,a5 as be,i as _,j as se,r as B,H as he,I as Ee,M as ke,K as we,P as Me,a2 as Ie,ai as Se,Q as Te,a3 as Be,aj as De}from"./index-42a788ad.js";import{I as j,u as q,J as ne,K as Q,L as Le,M as Ae,N as Fe,b as G,d as x,t as Oe,C as Pe,f as $e,_ as le,O as ze,H as Ne,P as Re,x as Ye,Q as He,R as Z,w as Ke}from"./request-fdc4c150.js";import{P as V}from"./vnode-cdc7eacd.js";import{F as Ue,E as Ve}from"./focus-trap-b46ba44e.js";import{t as We,U as ae,u as ee}from"./base-228d64a5.js";import{g as Xe}from"./scroll-5e7bdf7f.js";import{u as oe}from"./index-7c55feb4.js";const _e=(...e)=>t=>{e.forEach(l=>{de(l)?l(t):l.value=t})},je=(e,t,l)=>{let a={offsetX:0,offsetY:0};const u=n=>{const c=n.clientX,v=n.clientY,{offsetX:f,offsetY:g}=a,m=e.value.getBoundingClientRect(),i=m.left,b=m.top,L=m.width,$=m.height,z=document.documentElement.clientWidth,A=document.documentElement.clientHeight,N=-i+f,F=-b+g,w=z-i-L+f,M=A-b-$+g,O=E=>{const s=Math.min(Math.max(f+E.clientX-c,N),w),C=Math.min(Math.max(g+E.clientY-v,F),M);a={offsetX:s,offsetY:C},e.value.style.transform=`translate(${j(s)}, ${j(C)})`},p=()=>{document.removeEventListener("mousemove",O),document.removeEventListener("mouseup",p)};document.addEventListener("mousemove",O),document.addEventListener("mouseup",p)},d=()=>{t.value&&e.value&&t.value.addEventListener("mousedown",u)},r=()=>{t.value&&e.value&&t.value.removeEventListener("mousedown",u)};te(()=>{fe(()=>{l.value?d():r()})}),me(()=>{r()})},xe=(e,t={})=>{ve(e)||We("[useLockscreen]","You need to pass a ref param to this function");const l=t.ns||q("popup"),a=pe(()=>l.bm("parent","hidden"));if(!ne||Q(document.body,a.value))return;let u=0,d=!1,r="0";const n=()=>{setTimeout(()=>{Fe(document==null?void 0:document.body,a.value),d&&document&&(document.body.style.width=r)},200)};W(e,c=>{if(!c){n();return}d=!Q(document.body,a.value),d&&(r=document.body.style.width),u=Xe(l.namespace.value);const v=document.documentElement.clientHeight<document.body.scrollHeight,f=Le(document.body,"overflowY");u>0&&(v||f==="scroll")&&d&&(document.body.style.width=`calc(100% - ${u}px)`),Ae(document.body,a.value)}),ye(()=>n())},re=e=>{if(!e)return{onClick:K,onMousedown:K,onMouseup:K};let t=!1,l=!1;return{onClick:r=>{t&&l&&e(r),t=l=!1},onMousedown:r=>{t=r.target===r.currentTarget},onMouseup:r=>{l=r.target===r.currentTarget}}},qe=G({mask:{type:Boolean,default:!0},customMaskEvent:{type:Boolean,default:!1},overlayClass:{type:x([String,Array,Object])},zIndex:{type:x([String,Number])}}),Ge={click:e=>e instanceof MouseEvent},Je="overlay";var Qe=Y({name:"ElOverlay",props:qe,emits:Ge,setup(e,{slots:t,emit:l}){const a=q(Je),u=c=>{l("click",c)},{onClick:d,onMousedown:r,onMouseup:n}=re(e.customMaskEvent?void 0:u);return()=>e.mask?R("div",{class:[a.b(),e.overlayClass],style:{zIndex:e.zIndex},onClick:d,onMousedown:r,onMouseup:n},[h(t,"default")],V.STYLE|V.CLASS|V.PROPS,["onClick","onMouseup","onMousedown"]):ge("div",{class:e.overlayClass,style:{zIndex:e.zIndex,position:"fixed",top:"0px",right:"0px",bottom:"0px",left:"0px"}},[h(t,"default")])}});const Ze=Qe,ie=Symbol("dialogInjectionKey"),ue=G({center:Boolean,alignCenter:Boolean,closeIcon:{type:Oe},customClass:{type:String,default:""},draggable:Boolean,fullscreen:Boolean,showClose:{type:Boolean,default:!0},title:{type:String,default:""},ariaLevel:{type:String,default:"2"}}),eo={close:()=>!0},oo=["aria-level"],to=["aria-label"],so=["id"],no=Y({name:"ElDialogContent"}),lo=Y({...no,props:ue,emits:eo,setup(e){const t=e,{t:l}=Pe(),{Close:a}=ze,{dialogRef:u,headerRef:d,bodyId:r,ns:n,style:c}=J(ie),{focusTrapRef:v}=J(Ue),f=D(()=>[n.b(),n.is("fullscreen",t.fullscreen),n.is("draggable",t.draggable),n.is("align-center",t.alignCenter),{[n.m("center")]:t.center},t.customClass]),g=_e(v,u),m=D(()=>t.draggable);return je(u,d,m),(i,b)=>(P(),U("div",{ref:o(g),class:k(o(f)),style:se(o(c)),tabindex:"-1"},[H("header",{ref_key:"headerRef",ref:d,class:k(o(n).e("header"))},[h(i.$slots,"header",{},()=>[H("span",{role:"heading","aria-level":i.ariaLevel,class:k(o(n).e("title"))},Ce(i.title),11,oo)]),i.showClose?(P(),U("button",{key:0,"aria-label":o(l)("el.dialog.close"),class:k(o(n).e("headerbtn")),type:"button",onClick:b[0]||(b[0]=L=>i.$emit("close"))},[R(o($e),{class:k(o(n).e("close"))},{default:T(()=>[(P(),X(be(i.closeIcon||o(a))))]),_:1},8,["class"])],10,to)):_("v-if",!0)],2),H("div",{id:o(r),class:k(o(n).e("body"))},[h(i.$slots,"default")],10,so),i.$slots.footer?(P(),U("footer",{key:0,class:k(o(n).e("footer"))},[h(i.$slots,"footer")],2)):_("v-if",!0)],6))}});var ao=le(lo,[["__file","/home/runner/work/element-plus/element-plus/packages/components/dialog/src/dialog-content.vue"]]);const ro=G({...ue,appendToBody:Boolean,beforeClose:{type:x(Function)},destroyOnClose:Boolean,closeOnClickModal:{type:Boolean,default:!0},closeOnPressEscape:{type:Boolean,default:!0},lockScroll:{type:Boolean,default:!0},modal:{type:Boolean,default:!0},openDelay:{type:Number,default:0},closeDelay:{type:Number,default:0},top:{type:String},modelValue:Boolean,modalClass:String,width:{type:[String,Number]},zIndex:{type:Number},trapFocus:{type:Boolean,default:!1},headerAriaLevel:{type:String,default:"2"}}),io={open:()=>!0,opened:()=>!0,close:()=>!0,closed:()=>!0,[ae]:e=>Ne(e),openAutoFocus:()=>!0,closeAutoFocus:()=>!0},uo=(e,t)=>{const a=Ee().emit,{nextZIndex:u}=Re();let d="";const r=ee(),n=ee(),c=B(!1),v=B(!1),f=B(!1),g=B(e.zIndex||u());let m,i;const b=Ye("namespace",He),L=D(()=>{const y={},S=`--${b.value}-dialog`;return e.fullscreen||(e.top&&(y[`${S}-margin-top`]=e.top),e.width&&(y[`${S}-width`]=j(e.width))),y}),$=D(()=>e.alignCenter?{display:"flex"}:{});function z(){a("opened")}function A(){a("closed"),a(ae,!1),e.destroyOnClose&&(f.value=!1)}function N(){a("close")}function F(){i==null||i(),m==null||m(),e.openDelay&&e.openDelay>0?{stop:m}=Z(()=>p(),e.openDelay):p()}function w(){m==null||m(),i==null||i(),e.closeDelay&&e.closeDelay>0?{stop:i}=Z(()=>E(),e.closeDelay):E()}function M(){function y(S){S||(v.value=!0,c.value=!1)}e.beforeClose?e.beforeClose(y):w()}function O(){e.closeOnClickModal&&M()}function p(){ne&&(c.value=!0)}function E(){c.value=!1}function s(){a("openAutoFocus")}function C(){a("closeAutoFocus")}function I(y){var S;((S=y.detail)==null?void 0:S.focusReason)==="pointer"&&y.preventDefault()}e.lockScroll&&xe(c);function ce(){e.closeOnPressEscape&&M()}return W(()=>e.modelValue,y=>{y?(v.value=!1,F(),f.value=!0,g.value=e.zIndex?g.value++:u(),he(()=>{a("open"),t.value&&(t.value.scrollTop=0)})):c.value&&w()}),W(()=>e.fullscreen,y=>{t.value&&(y?(d=t.value.style.transform,t.value.style.transform=""):t.value.style.transform=d)}),te(()=>{e.modelValue&&(c.value=!0,f.value=!0,F())}),{afterEnter:z,afterLeave:A,beforeLeave:N,handleClose:M,onModalClick:O,close:w,doClose:E,onOpenAutoFocus:s,onCloseAutoFocus:C,onCloseRequested:ce,onFocusoutPrevented:I,titleId:r,bodyId:n,closed:v,style:L,overlayDialogStyle:$,rendered:f,visible:c,zIndex:g}},co=["aria-label","aria-labelledby","aria-describedby"],fo=Y({name:"ElDialog",inheritAttrs:!1}),mo=Y({...fo,props:ro,emits:io,setup(e,{expose:t}){const l=e,a=ke();oe({scope:"el-dialog",from:"the title slot",replacement:"the header slot",version:"3.0.0",ref:"https://element-plus.org/en-US/component/dialog.html#slots"},D(()=>!!a.title)),oe({scope:"el-dialog",from:"custom-class",replacement:"class",version:"2.3.0",ref:"https://element-plus.org/en-US/component/dialog.html#attributes",type:"Attribute"},D(()=>!!l.customClass));const u=q("dialog"),d=B(),r=B(),n=B(),{visible:c,titleId:v,bodyId:f,style:g,overlayDialogStyle:m,rendered:i,zIndex:b,afterEnter:L,afterLeave:$,beforeLeave:z,handleClose:A,onModalClick:N,onOpenAutoFocus:F,onCloseAutoFocus:w,onCloseRequested:M,onFocusoutPrevented:O}=uo(l,d);we(ie,{dialogRef:d,headerRef:r,bodyId:f,ns:u,rendered:i,style:g});const p=re(N),E=D(()=>l.draggable&&!l.fullscreen);return t({visible:c,dialogContentRef:n}),(s,C)=>(P(),X(De,{to:"body",disabled:!s.appendToBody},[R(Be,{name:"dialog-fade",onAfterEnter:o(L),onAfterLeave:o($),onBeforeLeave:o(z),persisted:""},{default:T(()=>[Me(R(o(Ze),{"custom-mask-event":"",mask:s.modal,"overlay-class":s.modalClass,"z-index":o(b)},{default:T(()=>[H("div",{role:"dialog","aria-modal":"true","aria-label":s.title||void 0,"aria-labelledby":s.title?void 0:o(v),"aria-describedby":o(f),class:k(`${o(u).namespace.value}-overlay-dialog`),style:se(o(m)),onClick:C[0]||(C[0]=(...I)=>o(p).onClick&&o(p).onClick(...I)),onMousedown:C[1]||(C[1]=(...I)=>o(p).onMousedown&&o(p).onMousedown(...I)),onMouseup:C[2]||(C[2]=(...I)=>o(p).onMouseup&&o(p).onMouseup(...I))},[R(o(Ve),{loop:"",trapped:o(c),"focus-start-el":"container",onFocusAfterTrapped:o(F),onFocusAfterReleased:o(w),onFocusoutPrevented:o(O),onReleaseRequested:o(M)},{default:T(()=>[o(i)?(P(),X(ao,Ie({key:0,ref_key:"dialogContentRef",ref:n},s.$attrs,{"custom-class":s.customClass,center:s.center,"align-center":s.alignCenter,"close-icon":s.closeIcon,draggable:o(E),fullscreen:s.fullscreen,"show-close":s.showClose,title:s.title,"aria-level":s.headerAriaLevel,onClose:o(A)}),Se({header:T(()=>[s.$slots.title?h(s.$slots,"title",{key:1}):h(s.$slots,"header",{key:0,close:o(A),titleId:o(v),titleClass:o(u).e("title")})]),default:T(()=>[h(s.$slots,"default")]),_:2},[s.$slots.footer?{name:"footer",fn:T(()=>[h(s.$slots,"footer")])}:void 0]),1040,["custom-class","center","align-center","close-icon","draggable","fullscreen","show-close","title","aria-level","onClose"])):_("v-if",!0)]),_:3},8,["trapped","onFocusAfterTrapped","onFocusAfterReleased","onFocusoutPrevented","onReleaseRequested"])],46,co)]),_:3},8,["mask","overlay-class","z-index"]),[[Te,o(c)]])]),_:3},8,["onAfterEnter","onAfterLeave","onBeforeLeave"])],8,["disabled"]))}});var vo=le(mo,[["__file","/home/runner/work/element-plus/element-plus/packages/components/dialog/src/dialog.vue"]]);const ko=Ke(vo);export{ko as E};
