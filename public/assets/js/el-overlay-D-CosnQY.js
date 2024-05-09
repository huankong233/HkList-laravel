import{aO as ce,O as oe,al as fe,ab as me,aI as K,as as ve,B as x,aU as ye,aV as te,aW as J,C as j,aX as pe,aY as ge,aZ as be,a_ as Ce,a$ as U,x as Z,y as V,d as R,a as Y,W as E,ak as he,aj as we,aQ as Ee,A as Q,N as L,o as O,c as W,i as H,H as k,u as o,t as ke,f as S,e as _,a8 as Me,Q as Te,n as q,I as se,J as ne,b0 as Be,aH as Ie,b1 as Se,r as D,aw as De,b2 as Le,D as Ae,K as $e,b3 as G,a0 as Fe,av as Oe,V as Pe,w as Ne,aA as ze,b4 as Ye,a4 as Re,ae as He,b5 as Ve,a5 as Ue}from"./index-B8pW1vfL.js";import{P as X}from"./vnode-CTXkDMm_.js";import{F as We,i as Xe,E as Ke}from"./focus-trap-Cx3G5mYf.js";import{t as je,U as le,u as ee}from"./base-sonieuUH.js";import{g as _e}from"./scroll-uoENNN5t.js";const qe=(...e)=>t=>{e.forEach(a=>{ce(a)?a(t):a.value=t})},xe=(e,t,a,c)=>{let n={offsetX:0,offsetY:0};const u=m=>{const d=m.clientX,y=m.clientY,{offsetX:p,offsetY:C}=n,f=e.value.getBoundingClientRect(),r=f.left,w=f.top,A=f.width,P=f.height,$=document.documentElement.clientWidth,N=document.documentElement.clientHeight,z=-r+p,F=-w+C,M=$-r-A+p,T=N-w-P+C,h=s=>{let v=p+s.clientX-d,g=C+s.clientY-y;c!=null&&c.value||(v=Math.min(Math.max(v,z),M),g=Math.min(Math.max(g,F),T)),n={offsetX:v,offsetY:g},e.value&&(e.value.style.transform=`translate(${K(v)}, ${K(g)})`)},B=()=>{document.removeEventListener("mousemove",h),document.removeEventListener("mouseup",B)};document.addEventListener("mousemove",h),document.addEventListener("mouseup",B)},i=()=>{t.value&&e.value&&t.value.addEventListener("mousedown",u)},l=()=>{t.value&&e.value&&t.value.removeEventListener("mousedown",u)};oe(()=>{fe(()=>{a.value?i():l()})}),me(()=>{l()})},Ze=(e,t={})=>{ve(e)||je("[useLockscreen]","You need to pass a ref param to this function");const a=t.ns||x("popup"),c=ye(()=>a.bm("parent","hidden"));if(!te||J(document.body,c.value))return;let n=0,u=!1,i="0";const l=()=>{setTimeout(()=>{Ce(document==null?void 0:document.body,c.value),u&&document&&(document.body.style.width=i)},200)};j(e,m=>{if(!m){l();return}u=!J(document.body,c.value),u&&(i=document.body.style.width),n=_e(a.namespace.value);const d=document.documentElement.clientHeight<document.body.scrollHeight,y=pe(document.body,"overflowY");n>0&&(d||y==="scroll")&&u&&(document.body.style.width=`calc(100% - ${n}px)`),ge(document.body,c.value)}),be(()=>l())},ae=e=>{if(!e)return{onClick:U,onMousedown:U,onMouseup:U};let t=!1,a=!1;return{onClick:i=>{t&&a&&e(i),t=a=!1},onMousedown:i=>{t=i.target===i.currentTarget},onMouseup:i=>{a=i.target===i.currentTarget}}},Je=Z({mask:{type:Boolean,default:!0},customMaskEvent:{type:Boolean,default:!1},overlayClass:{type:V([String,Array,Object])},zIndex:{type:V([String,Number])}}),Qe={click:e=>e instanceof MouseEvent},Ge="overlay";var eo=R({name:"ElOverlay",props:Je,emits:Qe,setup(e,{slots:t,emit:a}){const c=x(Ge),n=m=>{a("click",m)},{onClick:u,onMousedown:i,onMouseup:l}=ae(e.customMaskEvent?void 0:n);return()=>e.mask?Y("div",{class:[c.b(),e.overlayClass],style:{zIndex:e.zIndex},onClick:u,onMousedown:i,onMouseup:l},[E(t,"default")],X.STYLE|X.CLASS|X.PROPS,["onClick","onMouseup","onMousedown"]):he("div",{class:e.overlayClass,style:{zIndex:e.zIndex,position:"fixed",top:"0px",right:"0px",bottom:"0px",left:"0px"}},[E(t,"default")])}});const oo=eo,ie=Symbol("dialogInjectionKey"),re=Z({center:Boolean,alignCenter:Boolean,closeIcon:{type:we},draggable:Boolean,overflow:Boolean,fullscreen:Boolean,showClose:{type:Boolean,default:!0},title:{type:String,default:""},ariaLevel:{type:String,default:"2"}}),to={close:()=>!0},so=["aria-level"],no=["aria-label"],lo=["id"],ao=R({name:"ElDialogContent"}),io=R({...ao,props:re,emits:to,setup(e){const t=e,{t:a}=Ee(),{Close:c}=Be,{dialogRef:n,headerRef:u,bodyId:i,ns:l,style:m}=Q(ie),{focusTrapRef:d}=Q(We),y=L(()=>[l.b(),l.is("fullscreen",t.fullscreen),l.is("draggable",t.draggable),l.is("align-center",t.alignCenter),{[l.m("center")]:t.center}]),p=qe(d,n),C=L(()=>t.draggable),f=L(()=>t.overflow);return xe(n,u,C,f),(r,w)=>(O(),W("div",{ref:o(p),class:k(o(y)),style:se(o(m)),tabindex:"-1"},[H("header",{ref_key:"headerRef",ref:u,class:k([o(l).e("header"),{"show-close":r.showClose}])},[E(r.$slots,"header",{},()=>[H("span",{role:"heading","aria-level":r.ariaLevel,class:k(o(l).e("title"))},ke(r.title),11,so)]),r.showClose?(O(),W("button",{key:0,"aria-label":o(a)("el.dialog.close"),class:k(o(l).e("headerbtn")),type:"button",onClick:w[0]||(w[0]=A=>r.$emit("close"))},[Y(o(Te),{class:k(o(l).e("close"))},{default:S(()=>[(O(),_(Me(r.closeIcon||o(c))))]),_:1},8,["class"])],10,no)):q("v-if",!0)],2),H("div",{id:o(i),class:k(o(l).e("body"))},[E(r.$slots,"default")],10,lo),r.$slots.footer?(O(),W("footer",{key:0,class:k(o(l).e("footer"))},[E(r.$slots,"footer")],2)):q("v-if",!0)],6))}});var ro=ne(io,[["__file","dialog-content.vue"]]);const uo=Z({...re,appendToBody:Boolean,appendTo:{type:V(String),default:"body"},beforeClose:{type:V(Function)},destroyOnClose:Boolean,closeOnClickModal:{type:Boolean,default:!0},closeOnPressEscape:{type:Boolean,default:!0},lockScroll:{type:Boolean,default:!0},modal:{type:Boolean,default:!0},openDelay:{type:Number,default:0},closeDelay:{type:Number,default:0},top:{type:String},modelValue:Boolean,modalClass:String,width:{type:[String,Number]},zIndex:{type:Number},trapFocus:{type:Boolean,default:!1},headerAriaLevel:{type:String,default:"2"}}),co={open:()=>!0,opened:()=>!0,close:()=>!0,closed:()=>!0,[le]:e=>Ie(e),openAutoFocus:()=>!0,closeAutoFocus:()=>!0},fo=(e,t)=>{var a;const n=$e().emit,{nextZIndex:u}=Se();let i="";const l=ee(),m=ee(),d=D(!1),y=D(!1),p=D(!1),C=D((a=e.zIndex)!=null?a:u());let f,r;const w=De("namespace",Le),A=L(()=>{const b={},I=`--${w.value}-dialog`;return e.fullscreen||(e.top&&(b[`${I}-margin-top`]=e.top),e.width&&(b[`${I}-width`]=K(e.width))),b}),P=L(()=>e.alignCenter?{display:"flex"}:{});function $(){n("opened")}function N(){n("closed"),n(le,!1),e.destroyOnClose&&(p.value=!1)}function z(){n("close")}function F(){r==null||r(),f==null||f(),e.openDelay&&e.openDelay>0?{stop:f}=G(()=>B(),e.openDelay):B()}function M(){f==null||f(),r==null||r(),e.closeDelay&&e.closeDelay>0?{stop:r}=G(()=>s(),e.closeDelay):s()}function T(){function b(I){I||(y.value=!0,d.value=!1)}e.beforeClose?e.beforeClose(b):M()}function h(){e.closeOnClickModal&&T()}function B(){te&&(d.value=!0)}function s(){d.value=!1}function v(){n("openAutoFocus")}function g(){n("closeAutoFocus")}function ue(b){var I;((I=b.detail)==null?void 0:I.focusReason)==="pointer"&&b.preventDefault()}e.lockScroll&&Ze(d);function de(){e.closeOnPressEscape&&T()}return j(()=>e.modelValue,b=>{b?(y.value=!1,F(),p.value=!0,C.value=Xe(e.zIndex)?u():C.value++,Ae(()=>{n("open"),t.value&&(t.value.scrollTop=0)})):d.value&&M()}),j(()=>e.fullscreen,b=>{t.value&&(b?(i=t.value.style.transform,t.value.style.transform=""):t.value.style.transform=i)}),oe(()=>{e.modelValue&&(d.value=!0,p.value=!0,F())}),{afterEnter:$,afterLeave:N,beforeLeave:z,handleClose:T,onModalClick:h,close:M,doClose:s,onOpenAutoFocus:v,onCloseAutoFocus:g,onCloseRequested:de,onFocusoutPrevented:ue,titleId:l,bodyId:m,closed:y,style:A,overlayDialogStyle:P,rendered:p,visible:d,zIndex:C}},mo=["aria-label","aria-labelledby","aria-describedby"],vo=R({name:"ElDialog",inheritAttrs:!1}),yo=R({...vo,props:uo,emits:co,setup(e,{expose:t}){const a=e,c=Fe();Oe({scope:"el-dialog",from:"the title slot",replacement:"the header slot",version:"3.0.0",ref:"https://element-plus.org/en-US/component/dialog.html#slots"},L(()=>!!c.title));const n=x("dialog"),u=D(),i=D(),l=D(),{visible:m,titleId:d,bodyId:y,style:p,overlayDialogStyle:C,rendered:f,zIndex:r,afterEnter:w,afterLeave:A,beforeLeave:P,handleClose:$,onModalClick:N,onOpenAutoFocus:z,onCloseAutoFocus:F,onCloseRequested:M,onFocusoutPrevented:T}=fo(a,u);Pe(ie,{dialogRef:u,headerRef:i,bodyId:y,ns:n,rendered:f,style:p});const h=ae(N),B=L(()=>a.draggable&&!a.fullscreen);return t({visible:m,dialogContentRef:l}),(s,v)=>(O(),_(Ve,{to:s.appendTo,disabled:s.appendTo!=="body"?!1:!s.appendToBody},[Y(He,{name:"dialog-fade",onAfterEnter:o(w),onAfterLeave:o(A),onBeforeLeave:o(P),persisted:""},{default:S(()=>[Ne(Y(o(oo),{"custom-mask-event":"",mask:s.modal,"overlay-class":s.modalClass,"z-index":o(r)},{default:S(()=>[H("div",{role:"dialog","aria-modal":"true","aria-label":s.title||void 0,"aria-labelledby":s.title?void 0:o(d),"aria-describedby":o(y),class:k(`${o(n).namespace.value}-overlay-dialog`),style:se(o(C)),onClick:v[0]||(v[0]=(...g)=>o(h).onClick&&o(h).onClick(...g)),onMousedown:v[1]||(v[1]=(...g)=>o(h).onMousedown&&o(h).onMousedown(...g)),onMouseup:v[2]||(v[2]=(...g)=>o(h).onMouseup&&o(h).onMouseup(...g))},[Y(o(Ke),{loop:"",trapped:o(m),"focus-start-el":"container",onFocusAfterTrapped:o(z),onFocusAfterReleased:o(F),onFocusoutPrevented:o(T),onReleaseRequested:o(M)},{default:S(()=>[o(f)?(O(),_(ro,ze({key:0,ref_key:"dialogContentRef",ref:l},s.$attrs,{center:s.center,"align-center":s.alignCenter,"close-icon":s.closeIcon,draggable:o(B),overflow:s.overflow,fullscreen:s.fullscreen,"show-close":s.showClose,title:s.title,"aria-level":s.headerAriaLevel,onClose:o($)}),Ye({header:S(()=>[s.$slots.title?E(s.$slots,"title",{key:1}):E(s.$slots,"header",{key:0,close:o($),titleId:o(d),titleClass:o(n).e("title")})]),default:S(()=>[E(s.$slots,"default")]),_:2},[s.$slots.footer?{name:"footer",fn:S(()=>[E(s.$slots,"footer")])}:void 0]),1040,["center","align-center","close-icon","draggable","overflow","fullscreen","show-close","title","aria-level","onClose"])):q("v-if",!0)]),_:3},8,["trapped","onFocusAfterTrapped","onFocusAfterReleased","onFocusoutPrevented","onReleaseRequested"])],46,mo)]),_:3},8,["mask","overlay-class","z-index"]),[[Re,o(m)]])]),_:3},8,["onAfterEnter","onAfterLeave","onBeforeLeave"])],8,["to","disabled"]))}});var po=ne(yo,[["__file","dialog.vue"]]);const Eo=Ue(po);export{Eo as E};