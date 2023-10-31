import{C as Se,D as $e,a as z,E as ee,r as N,G as R,H as te,e as ae,c as de,n as be,u as C,j as xe,I as q,g as I,o as fe,J as ke,b as a,K as Be,h as ve,L as Oe,M as Ae,N as Re,O as Ve,P as Le,Q as ze,i as me,k as Ie,y as le,R as De,w as O,p as Me,q as Y,z as Fe,A as Ue,f as M,s as Ke,_ as F}from"./index-42a788ad.js";import{t as se,U as pe}from"./base-228d64a5.js";import{E as qe}from"./el-card-18fddc5f.js";import{E as He}from"./el-text-d45bd844.js";import{E as je}from"./el-button-71aeade6.js";import{a as Ge}from"./admin-b8fd26c3.js";import{b as H,d as ne,m as _e,u as j,a as he,_ as ge,c as We,e as Je,f as U,g as Qe,h as Xe,j as Ye,k as K,p as Ze,l as re,n as et,o as ie,w as tt,q as at,E as ce}from"./request-fdc4c150.js";import{c as A}from"./strings-88b4a059.js";import{f as st}from"./vnode-cdc7eacd.js";import{u as nt}from"./index-7c55feb4.js";import"./use-form-item-6eb8476f.js";const ot=(e,s,u)=>st(e.subTree).filter(t=>{var n;return $e(t)&&((n=t.type)==null?void 0:n.name)===s&&!!t.component}).map(t=>t.component.uid).map(t=>u[t]).filter(t=>!!t),lt=(e,s)=>{const u={},g=Se([]);return{children:g,addChild:n=>{u[n.uid]=n,g.value=ot(e,s,u)},removeChild:n=>{delete u[n],g.value=g.value.filter($=>$.uid!==n)}}},G=Symbol("tabsRootContextKey"),rt=H({tabs:{type:ne(Array),default:()=>_e([])}}),ye="ElTabBar",it=z({name:ye}),ct=z({...it,props:rt,setup(e,{expose:s}){const u=e,g=q(),c=ee(G);c||se(ye,"<el-tabs><el-tab-bar /></el-tabs>");const t=j("tabs"),n=N(),$=N(),v=()=>{let p=0,d=0;const b=["top","bottom"].includes(c.props.tabPosition)?"width":"height",o=b==="width"?"x":"y",k=o==="x"?"left":"top";return u.tabs.every(w=>{var x,l;const h=(l=(x=g.parent)==null?void 0:x.refs)==null?void 0:l[`tab-${w.uid}`];if(!h)return!1;if(!w.active)return!0;p=h[`offset${A(k)}`],d=h[`client${A(b)}`];const E=window.getComputedStyle(h);return b==="width"&&(u.tabs.length>1&&(d-=Number.parseFloat(E.paddingLeft)+Number.parseFloat(E.paddingRight)),p+=Number.parseFloat(E.paddingLeft)),!1}),{[b]:`${d}px`,transform:`translate${A(o)}(${p}px)`}},m=()=>$.value=v();return R(()=>u.tabs,async()=>{await te(),m()},{immediate:!0}),he(n,()=>m()),s({ref:n,update:m}),(p,d)=>(ae(),de("div",{ref_key:"barRef",ref:n,class:be([C(t).e("active-bar"),C(t).is(C(c).props.tabPosition)]),style:xe($.value)},null,6))}});var ut=ge(ct,[["__file","/home/runner/work/element-plus/element-plus/packages/components/tabs/src/tab-bar.vue"]]);const dt=H({panes:{type:ne(Array),default:()=>_e([])},currentName:{type:[String,Number],default:""},editable:Boolean,type:{type:String,values:["card","border-card",""],default:""},stretch:Boolean}),bt={tabClick:(e,s,u)=>u instanceof Event,tabRemove:(e,s)=>s instanceof Event},ue="ElTabNav",ft=z({name:ue,props:dt,emits:bt,setup(e,{expose:s,emit:u}){const g=q(),c=ee(G);c||se(ue,"<el-tabs><tab-nav /></el-tabs>");const t=j("tabs"),n=We(),$=Je(),v=N(),m=N(),p=N(),d=N(),b=N(!1),o=N(0),k=N(!1),w=N(!0),x=I(()=>["top","bottom"].includes(c.props.tabPosition)?"width":"height"),l=I(()=>({transform:`translate${x.value==="width"?"X":"Y"}(-${o.value}px)`})),h=()=>{if(!v.value)return;const i=v.value[`offset${A(x.value)}`],f=o.value;if(!f)return;const r=f>i?f-i:0;o.value=r},E=()=>{if(!v.value||!m.value)return;const i=m.value[`offset${A(x.value)}`],f=v.value[`offset${A(x.value)}`],r=o.value;if(i-r<=f)return;const T=i-r>f*2?r+f:i-f;o.value=T},V=async()=>{const i=m.value;if(!b.value||!p.value||!v.value||!i)return;await te();const f=p.value.querySelector(".is-active");if(!f)return;const r=v.value,T=["top","bottom"].includes(c.props.tabPosition),P=f.getBoundingClientRect(),y=r.getBoundingClientRect(),B=T?i.offsetWidth-y.width:i.offsetHeight-y.height,S=o.value;let _=S;T?(P.left<y.left&&(_=S-(y.left-P.left)),P.right>y.right&&(_=S+P.right-y.right)):(P.top<y.top&&(_=S-(y.top-P.top)),P.bottom>y.bottom&&(_=S+(P.bottom-y.bottom))),_=Math.max(_,0),o.value=Math.min(_,B)},D=()=>{var i;if(!m.value||!v.value)return;e.stretch&&((i=d.value)==null||i.update());const f=m.value[`offset${A(x.value)}`],r=v.value[`offset${A(x.value)}`],T=o.value;r<f?(b.value=b.value||{},b.value.prev=T,b.value.next=T+r<f,f-T<r&&(o.value=f-r)):(b.value=!1,T>0&&(o.value=0))},Ee=i=>{const f=i.code,{up:r,down:T,left:P,right:y}=K;if(![r,T,P,y].includes(f))return;const B=Array.from(i.currentTarget.querySelectorAll("[role=tab]:not(.is-disabled)")),S=B.indexOf(i.target);let _;f===P||f===r?S===0?_=B.length-1:_=S-1:S<B.length-1?_=S+1:_=0,B[_].focus({preventScroll:!0}),B[_].click(),oe()},oe=()=>{w.value&&(k.value=!0)},W=()=>k.value=!1;return R(n,i=>{i==="hidden"?w.value=!1:i==="visible"&&setTimeout(()=>w.value=!0,50)}),R($,i=>{i?setTimeout(()=>w.value=!0,50):w.value=!1}),he(p,D),fe(()=>setTimeout(()=>V(),0)),ke(()=>D()),s({scrollToActiveTab:V,removeFocus:W}),R(()=>e.panes,()=>g.update(),{flush:"post",deep:!0}),()=>{const i=b.value?[a("span",{class:[t.e("nav-prev"),t.is("disabled",!b.value.prev)],onClick:h},[a(U,null,{default:()=>[a(Qe,null,null)]})]),a("span",{class:[t.e("nav-next"),t.is("disabled",!b.value.next)],onClick:E},[a(U,null,{default:()=>[a(Xe,null,null)]})])]:null,f=e.panes.map((r,T)=>{var P,y,B,S;const _=r.uid,J=r.props.disabled,Q=(y=(P=r.props.name)!=null?P:r.index)!=null?y:`${T}`,X=!J&&(r.isClosable||e.editable);r.index=`${T}`;const Te=X?a(U,{class:"is-icon-close",onClick:L=>u("tabRemove",r,L)},{default:()=>[a(Ye,null,null)]}):null,Pe=((S=(B=r.slots).label)==null?void 0:S.call(B))||r.props.label,we=!J&&r.active?0:-1;return a("div",{ref:`tab-${_}`,class:[t.e("item"),t.is(c.props.tabPosition),t.is("active",r.active),t.is("disabled",J),t.is("closable",X),t.is("focus",k.value)],id:`tab-${Q}`,key:`tab-${_}`,"aria-controls":`pane-${Q}`,role:"tab","aria-selected":r.active,tabindex:we,onFocus:()=>oe(),onBlur:()=>W(),onClick:L=>{W(),u("tabClick",r,Q,L)},onKeydown:L=>{X&&(L.code===K.delete||L.code===K.backspace)&&u("tabRemove",r,L)}},[Pe,Te])});return a("div",{ref:p,class:[t.e("nav-wrap"),t.is("scrollable",!!b.value),t.is(c.props.tabPosition)]},[i,a("div",{class:t.e("nav-scroll"),ref:v},[a("div",{class:[t.e("nav"),t.is(c.props.tabPosition),t.is("stretch",e.stretch&&["top","bottom"].includes(c.props.tabPosition))],ref:m,style:l.value,role:"tablist",onKeydown:Ee},[e.type?null:a(ut,{ref:d,tabs:[...e.panes]},null),f])])])}}}),vt=H({type:{type:String,values:["card","border-card",""],default:""},activeName:{type:[String,Number]},closable:Boolean,addable:Boolean,modelValue:{type:[String,Number]},editable:Boolean,tabPosition:{type:String,values:["top","right","bottom","left"],default:"top"},beforeLeave:{type:ne(Function),default:()=>!0},stretch:Boolean}),Z=e=>Oe(e)||et(e),mt={[pe]:e=>Z(e),tabClick:(e,s)=>s instanceof Event,tabChange:e=>Z(e),edit:(e,s)=>["remove","add"].includes(s),tabRemove:e=>Z(e),tabAdd:()=>!0};var pt=z({name:"ElTabs",props:vt,emits:mt,setup(e,{emit:s,slots:u,expose:g}){var c,t;const n=j("tabs"),{children:$,addChild:v,removeChild:m}=lt(q(),"ElTabPane"),p=N(),d=N((t=(c=e.modelValue)!=null?c:e.activeName)!=null?t:"0"),b=l=>{d.value=l,s(pe,l),s("tabChange",l)},o=async l=>{var h,E,V;if(!(d.value===l||re(l)))try{await((h=e.beforeLeave)==null?void 0:h.call(e,l,d.value))!==!1&&(b(l),(V=(E=p.value)==null?void 0:E.removeFocus)==null||V.call(E))}catch{}},k=(l,h,E)=>{l.props.disabled||(o(h),s("tabClick",l,E))},w=(l,h)=>{l.props.disabled||re(l.props.name)||(h.stopPropagation(),s("edit",l.props.name,"remove"),s("tabRemove",l.props.name))},x=()=>{s("edit",void 0,"add"),s("tabAdd")};return nt({from:'"activeName"',replacement:'"model-value" or "v-model"',scope:"ElTabs",version:"2.3.0",ref:"https://element-plus.org/en-US/component/tabs.html#attributes",type:"Attribute"},I(()=>!!e.activeName)),R(()=>e.activeName,l=>o(l)),R(()=>e.modelValue,l=>o(l)),R(d,async()=>{var l;await te(),(l=p.value)==null||l.scrollToActiveTab()}),Be(G,{props:e,currentName:d,registerPane:v,unregisterPane:m}),g({currentName:d}),()=>{const l=e.editable||e.addable?a("span",{class:n.e("new-tab"),tabindex:"0",onClick:x,onKeydown:V=>{V.code===K.enter&&x()}},[a(U,{class:n.is("icon-plus")},{default:()=>[a(Ze,null,null)]})]):null,h=a("div",{class:[n.e("header"),n.is(e.tabPosition)]},[l,a(ft,{ref:p,currentName:d.value,editable:e.editable,type:e.type,panes:$.value,stretch:e.stretch,onTabClick:k,onTabRemove:w},null)]),E=a("div",{class:n.e("content")},[ve(u,"default")]);return a("div",{class:[n.b(),n.m(e.tabPosition),{[n.m("card")]:e.type==="card",[n.m("border-card")]:e.type==="border-card"}]},[...e.tabPosition!=="bottom"?[h,E]:[E,h]])}}});const _t=H({label:{type:String,default:""},name:{type:[String,Number]},closable:Boolean,disabled:Boolean,lazy:Boolean}),ht=["id","aria-hidden","aria-labelledby"],Ne="ElTabPane",gt=z({name:Ne}),yt=z({...gt,props:_t,setup(e){const s=e,u=q(),g=Ae(),c=ee(G);c||se(Ne,"usage: <el-tabs><el-tab-pane /></el-tabs/>");const t=j("tab-pane"),n=N(),$=I(()=>s.closable||c.props.closable),v=ie(()=>{var o;return c.currentName.value===((o=s.name)!=null?o:n.value)}),m=N(v.value),p=I(()=>{var o;return(o=s.name)!=null?o:n.value}),d=ie(()=>!s.lazy||m.value||v.value);R(v,o=>{o&&(m.value=!0)});const b=Re({uid:u.uid,slots:g,props:s,paneName:p,active:v,index:n,isClosable:$});return fe(()=>{c.registerPane(b)}),Ve(()=>{c.unregisterPane(b.uid)}),(o,k)=>C(d)?Le((ae(),de("div",{key:0,id:`pane-${C(p)}`,class:be(C(t).b()),role:"tabpanel","aria-hidden":!C(v),"aria-labelledby":`tab-${C(p)}`},[ve(o.$slots,"default")],10,ht)),[[ze,C(v)]]):me("v-if",!0)}});var Ce=ge(yt,[["__file","/home/runner/work/element-plus/element-plus/packages/components/tabs/src/tab-pane.vue"]]);const Nt=tt(pt,{TabPane:Ce}),Ct=at(Ce);const Rt=z({__name:"AdminPannel",setup(e){const s=N("changeConfig"),u=Ie();le()==="0"&&(ce.error("请先登陆"),u.push("/login"));const g=M(()=>F(()=>import("./ChangeConfig-8a16482e.js"),["assets/js/ChangeConfig-8a16482e.js","assets/js/base-228d64a5.js","assets/js/request-fdc4c150.js","assets/js/index-42a788ad.js","assets/css/index-e5387e95.css","assets/css/base-34dba8e3.css","assets/js/el-loading-0dc4cfe0.js","assets/css/el-loading-d0f2d079.css","assets/js/el-form-item-e045bac0.js","assets/js/use-form-item-6eb8476f.js","assets/js/_initCloneObject-93a1e332.js","assets/css/el-form-item-7d5af5e1.css","assets/js/el-button-71aeade6.js","assets/js/index-7c55feb4.js","assets/css/el-button-2cb60ae5.css","assets/js/el-input-95f19ce8.js","assets/js/isNil-c75b1b34.js","assets/css/el-input-45b6b5ba.css","assets/js/admin-b8fd26c3.js","assets/js/validator-8025b128.js","assets/css/ChangeConfig-8bc5cac7.css"])),c=M(()=>F(()=>import("./ChangeUserInfo-fb832a3f.js"),["assets/js/ChangeUserInfo-fb832a3f.js","assets/js/base-228d64a5.js","assets/js/request-fdc4c150.js","assets/js/index-42a788ad.js","assets/css/index-e5387e95.css","assets/css/base-34dba8e3.css","assets/js/el-form-item-e045bac0.js","assets/js/use-form-item-6eb8476f.js","assets/js/_initCloneObject-93a1e332.js","assets/css/el-form-item-7d5af5e1.css","assets/js/el-button-71aeade6.js","assets/js/index-7c55feb4.js","assets/css/el-button-2cb60ae5.css","assets/js/el-input-95f19ce8.js","assets/js/isNil-c75b1b34.js","assets/css/el-input-45b6b5ba.css","assets/js/admin-b8fd26c3.js"])),t=M(()=>F(()=>import("./AddAccount-12f43344.js"),["assets/js/AddAccount-12f43344.js","assets/js/base-228d64a5.js","assets/js/request-fdc4c150.js","assets/js/index-42a788ad.js","assets/css/index-e5387e95.css","assets/css/base-34dba8e3.css","assets/js/el-overlay-f3b67021.js","assets/js/vnode-cdc7eacd.js","assets/js/focus-trap-b46ba44e.js","assets/js/isNil-c75b1b34.js","assets/js/scroll-5e7bdf7f.js","assets/js/index-7c55feb4.js","assets/css/el-overlay-49152403.css","assets/js/el-button-71aeade6.js","assets/js/use-form-item-6eb8476f.js","assets/css/el-button-2cb60ae5.css","assets/js/el-form-item-e045bac0.js","assets/js/_initCloneObject-93a1e332.js","assets/css/el-form-item-7d5af5e1.css","assets/js/el-input-95f19ce8.js","assets/css/el-input-45b6b5ba.css","assets/js/AccountManagement-1597ea6c.js","assets/js/admin-b8fd26c3.js"])),n=M(()=>F(()=>import("./AccountList-fc5b7c0b.js"),["assets/js/AccountList-fc5b7c0b.js","assets/js/base-228d64a5.js","assets/js/request-fdc4c150.js","assets/js/index-42a788ad.js","assets/css/index-e5387e95.css","assets/css/base-34dba8e3.css","assets/js/el-loading-0dc4cfe0.js","assets/css/el-loading-d0f2d079.css","assets/js/el-input-95f19ce8.js","assets/js/use-form-item-6eb8476f.js","assets/js/isNil-c75b1b34.js","assets/css/el-input-45b6b5ba.css","assets/js/el-popper-a3b50ee9.js","assets/js/_initCloneObject-93a1e332.js","assets/js/focus-trap-b46ba44e.js","assets/css/el-popper-9e0bc416.css","assets/js/el-select-f7a1ec8b.js","assets/js/strings-88b4a059.js","assets/js/index-7c55feb4.js","assets/js/scroll-5e7bdf7f.js","assets/js/validator-8025b128.js","assets/css/el-select-0b32213c.css","assets/js/el-table-column-d00bf6d8.js","assets/js/_commonjsHelpers-725317a4.js","assets/css/el-table-column-838a2945.css","assets/js/el-button-71aeade6.js","assets/css/el-button-2cb60ae5.css","assets/js/AccountManagement-1597ea6c.js","assets/js/admin-b8fd26c3.js","assets/js/_plugin-vue_export-helper-c27b6911.js","assets/css/AccountList-40c20a59.css"])),$=async()=>{await Ge(),ce.success("退出登陆成功~"),Ke("0"),u.push("/login")};return(v,m)=>{const p=je,d=Ct,b=He,o=qe,k=Nt;return C(le)()==="1"?(ae(),De(o,{key:0,class:"box-card"},{default:O(()=>[Me("h2",null,[Y(" 后台控制中心 | "+Fe(C(Ue)())+" ",1),a(p,{type:"danger",onClick:m[0]||(m[0]=w=>$())},{default:O(()=>[Y(" 退出登陆 ")]),_:1})]),a(k,{modelValue:s.value,"onUpdate:modelValue":m[1]||(m[1]=w=>s.value=w)},{default:O(()=>[a(d,{label:"基础配置",name:"changeConfig"},{default:O(()=>[a(C(g))]),_:1}),a(d,{label:"修改用户信息",name:"changeUserInfo"},{default:O(()=>[a(C(c))]),_:1}),a(d,{label:"代理账号管理",name:"accountManagement"},{default:O(()=>[a(C(t)),a(C(n))]),_:1}),a(d,{label:"开源说明",name:"openSourceNotice"},{default:O(()=>[a(o,null,{default:O(()=>[a(b,null,{default:O(()=>[Y(" 本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。 ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])]),_:1})):me("",!0)}}});export{Rt as default};
