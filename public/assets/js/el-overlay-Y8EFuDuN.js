import{a7 as ce,o as oe,T as fe,a9 as me,a1 as ve,ai as ye,G as X,W as pe,a0 as U,a as Y,b as R,h as E,aa as ge,E as J,g as L,e as A,c as V,p as H,n as k,u as o,z as be,w as B,R as j,a5 as Ce,i as _,j as te,r as D,H as he,I as we,M as Ee,K as ke,P as Me,a2 as Te,aj as Ie,Q as Se,a3 as Be,ak as De}from"./index-DW4GyZFB.js";import{J as q,u as x,K as se,L as Q,M as Le,N as Fe,O as Pe,b as G,d as K,t as $e,D as Ae,f as Oe,_ as ne,P as ze,I as Ne,Q as Re,x as Ye,R as He,S as Z,w as Ke}from"./request-Ckf9C3Wk.js";import{P as W}from"./vnode-Dqtg7S9T.js";import{F as Ue,i as Ve,E as We}from"./focus-trap-BR6sfOYh.js";import{t as Xe,U as le,a as ee,u as je}from"./base-D_zbSILa.js";import{g as _e}from"./scroll-DOTZrInm.js";const qe=(...e)=>t=>{e.forEach(a=>{ce(a)?a(t):a.value=t})},xe=(e,t,a,c)=>{let n={offsetX:0,offsetY:0};const u=m=>{const d=m.clientX,y=m.clientY,{offsetX:p,offsetY:C}=n,f=e.value.getBoundingClientRect(),r=f.left,w=f.top,F=f.width,O=f.height,P=document.documentElement.clientWidth,z=document.documentElement.clientHeight,N=-r+p,$=-w+C,M=P-r-F+p,T=z-w-O+C,h=s=>{let v=p+s.clientX-d,g=C+s.clientY-y;c!=null&&c.value||(v=Math.min(Math.max(v,N),M),g=Math.min(Math.max(g,$),T)),n={offsetX:v,offsetY:g},e.value&&(e.value.style.transform=`translate(${q(v)}, ${q(g)})`)},I=()=>{document.removeEventListener("mousemove",h),document.removeEventListener("mouseup",I)};document.addEventListener("mousemove",h),document.addEventListener("mouseup",I)},i=()=>{t.value&&e.value&&t.value.addEventListener("mousedown",u)},l=()=>{t.value&&e.value&&t.value.removeEventListener("mousedown",u)};oe(()=>{fe(()=>{a.value?i():l()})}),me(()=>{l()})},Ge=(e,t={})=>{ve(e)||Xe("[useLockscreen]","You need to pass a ref param to this function");const a=t.ns||x("popup"),c=ye(()=>a.bm("parent","hidden"));if(!se||Q(document.body,c.value))return;let n=0,u=!1,i="0";const l=()=>{setTimeout(()=>{Pe(document==null?void 0:document.body,c.value),u&&document&&(document.body.style.width=i)},200)};X(e,m=>{if(!m){l();return}u=!Q(document.body,c.value),u&&(i=document.body.style.width),n=_e(a.namespace.value);const d=document.documentElement.clientHeight<document.body.scrollHeight,y=Le(document.body,"overflowY");n>0&&(d||y==="scroll")&&u&&(document.body.style.width=`calc(100% - ${n}px)`),Fe(document.body,c.value)}),pe(()=>l())},ae=e=>{if(!e)return{onClick:U,onMousedown:U,onMouseup:U};let t=!1,a=!1;return{onClick:i=>{t&&a&&e(i),t=a=!1},onMousedown:i=>{t=i.target===i.currentTarget},onMouseup:i=>{a=i.target===i.currentTarget}}},Je=G({mask:{type:Boolean,default:!0},customMaskEvent:{type:Boolean,default:!1},overlayClass:{type:K([String,Array,Object])},zIndex:{type:K([String,Number])}}),Qe={click:e=>e instanceof MouseEvent},Ze="overlay";var eo=Y({name:"ElOverlay",props:Je,emits:Qe,setup(e,{slots:t,emit:a}){const c=x(Ze),n=m=>{a("click",m)},{onClick:u,onMousedown:i,onMouseup:l}=ae(e.customMaskEvent?void 0:n);return()=>e.mask?R("div",{class:[c.b(),e.overlayClass],style:{zIndex:e.zIndex},onClick:u,onMousedown:i,onMouseup:l},[E(t,"default")],W.STYLE|W.CLASS|W.PROPS,["onClick","onMouseup","onMousedown"]):ge("div",{class:e.overlayClass,style:{zIndex:e.zIndex,position:"fixed",top:"0px",right:"0px",bottom:"0px",left:"0px"}},[E(t,"default")])}});const oo=eo,ie=Symbol("dialogInjectionKey"),re=G({center:Boolean,alignCenter:Boolean,closeIcon:{type:$e},draggable:Boolean,overflow:Boolean,fullscreen:Boolean,showClose:{type:Boolean,default:!0},title:{type:String,default:""},ariaLevel:{type:String,default:"2"}}),to={close:()=>!0},so=["aria-level"],no=["aria-label"],lo=["id"],ao=Y({name:"ElDialogContent"}),io=Y({...ao,props:re,emits:to,setup(e){const t=e,{t:a}=Ae(),{Close:c}=ze,{dialogRef:n,headerRef:u,bodyId:i,ns:l,style:m}=J(ie),{focusTrapRef:d}=J(Ue),y=L(()=>[l.b(),l.is("fullscreen",t.fullscreen),l.is("draggable",t.draggable),l.is("align-center",t.alignCenter),{[l.m("center")]:t.center}]),p=qe(d,n),C=L(()=>t.draggable),f=L(()=>t.overflow);return xe(n,u,C,f),(r,w)=>(A(),V("div",{ref:o(p),class:k(o(y)),style:te(o(m)),tabindex:"-1"},[H("header",{ref_key:"headerRef",ref:u,class:k([o(l).e("header"),{"show-close":r.showClose}])},[E(r.$slots,"header",{},()=>[H("span",{role:"heading","aria-level":r.ariaLevel,class:k(o(l).e("title"))},be(r.title),11,so)]),r.showClose?(A(),V("button",{key:0,"aria-label":o(a)("el.dialog.close"),class:k(o(l).e("headerbtn")),type:"button",onClick:w[0]||(w[0]=F=>r.$emit("close"))},[R(o(Oe),{class:k(o(l).e("close"))},{default:B(()=>[(A(),j(Ce(r.closeIcon||o(c))))]),_:1},8,["class"])],10,no)):_("v-if",!0)],2),H("div",{id:o(i),class:k(o(l).e("body"))},[E(r.$slots,"default")],10,lo),r.$slots.footer?(A(),V("footer",{key:0,class:k(o(l).e("footer"))},[E(r.$slots,"footer")],2)):_("v-if",!0)],6))}});var ro=ne(io,[["__file","dialog-content.vue"]]);const uo=G({...re,appendToBody:Boolean,appendTo:{type:K(String),default:"body"},beforeClose:{type:K(Function)},destroyOnClose:Boolean,closeOnClickModal:{type:Boolean,default:!0},closeOnPressEscape:{type:Boolean,default:!0},lockScroll:{type:Boolean,default:!0},modal:{type:Boolean,default:!0},openDelay:{type:Number,default:0},closeDelay:{type:Number,default:0},top:{type:String},modelValue:Boolean,modalClass:String,width:{type:[String,Number]},zIndex:{type:Number},trapFocus:{type:Boolean,default:!1},headerAriaLevel:{type:String,default:"2"}}),co={open:()=>!0,opened:()=>!0,close:()=>!0,closed:()=>!0,[le]:e=>Ne(e),openAutoFocus:()=>!0,closeAutoFocus:()=>!0},fo=(e,t)=>{var a;const n=we().emit,{nextZIndex:u}=Re();let i="";const l=ee(),m=ee(),d=D(!1),y=D(!1),p=D(!1),C=D((a=e.zIndex)!=null?a:u());let f,r;const w=Ye("namespace",He),F=L(()=>{const b={},S=`--${w.value}-dialog`;return e.fullscreen||(e.top&&(b[`${S}-margin-top`]=e.top),e.width&&(b[`${S}-width`]=q(e.width))),b}),O=L(()=>e.alignCenter?{display:"flex"}:{});function P(){n("opened")}function z(){n("closed"),n(le,!1),e.destroyOnClose&&(p.value=!1)}function N(){n("close")}function $(){r==null||r(),f==null||f(),e.openDelay&&e.openDelay>0?{stop:f}=Z(()=>I(),e.openDelay):I()}function M(){f==null||f(),r==null||r(),e.closeDelay&&e.closeDelay>0?{stop:r}=Z(()=>s(),e.closeDelay):s()}function T(){function b(S){S||(y.value=!0,d.value=!1)}e.beforeClose?e.beforeClose(b):M()}function h(){e.closeOnClickModal&&T()}function I(){se&&(d.value=!0)}function s(){d.value=!1}function v(){n("openAutoFocus")}function g(){n("closeAutoFocus")}function ue(b){var S;((S=b.detail)==null?void 0:S.focusReason)==="pointer"&&b.preventDefault()}e.lockScroll&&Ge(d);function de(){e.closeOnPressEscape&&T()}return X(()=>e.modelValue,b=>{b?(y.value=!1,$(),p.value=!0,C.value=Ve(e.zIndex)?u():C.value++,he(()=>{n("open"),t.value&&(t.value.scrollTop=0)})):d.value&&M()}),X(()=>e.fullscreen,b=>{t.value&&(b?(i=t.value.style.transform,t.value.style.transform=""):t.value.style.transform=i)}),oe(()=>{e.modelValue&&(d.value=!0,p.value=!0,$())}),{afterEnter:P,afterLeave:z,beforeLeave:N,handleClose:T,onModalClick:h,close:M,doClose:s,onOpenAutoFocus:v,onCloseAutoFocus:g,onCloseRequested:de,onFocusoutPrevented:ue,titleId:l,bodyId:m,closed:y,style:F,overlayDialogStyle:O,rendered:p,visible:d,zIndex:C}},mo=["aria-label","aria-labelledby","aria-describedby"],vo=Y({name:"ElDialog",inheritAttrs:!1}),yo=Y({...vo,props:uo,emits:co,setup(e,{expose:t}){const a=e,c=Ee();je({scope:"el-dialog",from:"the title slot",replacement:"the header slot",version:"3.0.0",ref:"https://element-plus.org/en-US/component/dialog.html#slots"},L(()=>!!c.title));const n=x("dialog"),u=D(),i=D(),l=D(),{visible:m,titleId:d,bodyId:y,style:p,overlayDialogStyle:C,rendered:f,zIndex:r,afterEnter:w,afterLeave:F,beforeLeave:O,handleClose:P,onModalClick:z,onOpenAutoFocus:N,onCloseAutoFocus:$,onCloseRequested:M,onFocusoutPrevented:T}=fo(a,u);ke(ie,{dialogRef:u,headerRef:i,bodyId:y,ns:n,rendered:f,style:p});const h=ae(z),I=L(()=>a.draggable&&!a.fullscreen);return t({visible:m,dialogContentRef:l}),(s,v)=>(A(),j(De,{to:s.appendTo,disabled:s.appendTo!=="body"?!1:!s.appendToBody},[R(Be,{name:"dialog-fade",onAfterEnter:o(w),onAfterLeave:o(F),onBeforeLeave:o(O),persisted:""},{default:B(()=>[Me(R(o(oo),{"custom-mask-event":"",mask:s.modal,"overlay-class":s.modalClass,"z-index":o(r)},{default:B(()=>[H("div",{role:"dialog","aria-modal":"true","aria-label":s.title||void 0,"aria-labelledby":s.title?void 0:o(d),"aria-describedby":o(y),class:k(`${o(n).namespace.value}-overlay-dialog`),style:te(o(C)),onClick:v[0]||(v[0]=(...g)=>o(h).onClick&&o(h).onClick(...g)),onMousedown:v[1]||(v[1]=(...g)=>o(h).onMousedown&&o(h).onMousedown(...g)),onMouseup:v[2]||(v[2]=(...g)=>o(h).onMouseup&&o(h).onMouseup(...g))},[R(o(We),{loop:"",trapped:o(m),"focus-start-el":"container",onFocusAfterTrapped:o(N),onFocusAfterReleased:o($),onFocusoutPrevented:o(T),onReleaseRequested:o(M)},{default:B(()=>[o(f)?(A(),j(ro,Te({key:0,ref_key:"dialogContentRef",ref:l},s.$attrs,{center:s.center,"align-center":s.alignCenter,"close-icon":s.closeIcon,draggable:o(I),overflow:s.overflow,fullscreen:s.fullscreen,"show-close":s.showClose,title:s.title,"aria-level":s.headerAriaLevel,onClose:o(P)}),Ie({header:B(()=>[s.$slots.title?E(s.$slots,"title",{key:1}):E(s.$slots,"header",{key:0,close:o(P),titleId:o(d),titleClass:o(n).e("title")})]),default:B(()=>[E(s.$slots,"default")]),_:2},[s.$slots.footer?{name:"footer",fn:B(()=>[E(s.$slots,"footer")])}:void 0]),1040,["center","align-center","close-icon","draggable","overflow","fullscreen","show-close","title","aria-level","onClose"])):_("v-if",!0)]),_:3},8,["trapped","onFocusAfterTrapped","onFocusAfterReleased","onFocusoutPrevented","onReleaseRequested"])],46,mo)]),_:3},8,["mask","overlay-class","z-index"]),[[Se,o(m)]])]),_:3},8,["onAfterEnter","onAfterLeave","onBeforeLeave"])],8,["to","disabled"]))}});var po=ne(yo,[["__file","dialog.vue"]]);const ko=Ke(po);export{ko as E};
