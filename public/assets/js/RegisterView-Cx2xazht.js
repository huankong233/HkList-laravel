import{b as F,r as m,k as x,w as B,c as V,n as s,u as R,p as N,v as q,f as p,q as c,e as _,t as S,l as o,C as D,x as y,E as I,y as L,z as U,A,B as M}from"./.pnpm-TARA5JeW.js";import{g as $,u as h,a as z,r as K}from"./index--d9Pblwj.js";import{r as T,f as j}from"./registerkeyDown-DnxsfoLV.js";import{_ as G}from"./_plugin-vue_export-helper-DlAUqK2U.js";const H={class:"container"},J=["src"],O=F({__name:"RegisterView",setup(P){const l=R();$()==="1"&&l.push("/admin");const b=()=>l.push("/login"),n=m(!1),e=m({username:"",password:"",inv_code:""}),u=m(null),f={username:[{required:!0,message:"请输入用户名",trigger:"blur"}],password:[{required:!0,message:"请输入密码",trigger:"blur"}]},g=h();g.config.need_inv_code&&(f.inv_code=[{required:!0,message:"请输入邀请码",trigger:"blur"}]);const v=async i=>{if(!(!i||!await i.validate()))try{n.value=!0,await K({username:e.value.username,password:e.value.password,inv_code:e.value.inv_code}),I.success("注册成功"),l.push("/login")}finally{n.value=!1}};return T("Enter",()=>v(u.value)),(i,a)=>{const d=L,r=U,w=A,k=M,E=N,C=q;return p(),x("div",H,[B((p(),V(E,null,{default:s(()=>[c("h1",null,[c("img",{src:_(j),alt:"logo"},null,8,J)]),c("h2",null,"注册 | "+S(_(z)()),1),o(k,{ref_key:"registerFormRef",ref:u,model:e.value,rules:f,"label-width":"auto"},{default:s(()=>[o(r,{label:"用户名",prop:"username"},{default:s(()=>[o(d,{modelValue:e.value.username,"onUpdate:modelValue":a[0]||(a[0]=t=>e.value.username=t)},null,8,["modelValue"])]),_:1}),o(r,{label:"密码",prop:"password"},{default:s(()=>[o(d,{modelValue:e.value.password,"onUpdate:modelValue":a[1]||(a[1]=t=>e.value.password=t),type:"password"},null,8,["modelValue"])]),_:1}),_(g).config.need_inv_code?(p(),V(r,{key:0,label:"邀请码",prop:"inv_code"},{default:s(()=>[o(d,{modelValue:e.value.inv_code,"onUpdate:modelValue":a[2]||(a[2]=t=>e.value.inv_code=t)},null,8,["modelValue"])]),_:1})):D("",!0),o(r,{class:"center"},{default:s(()=>[o(w,{type:"primary",onClick:a[3]||(a[3]=t=>b())},{default:s(()=>[y("登陆")]),_:1}),o(w,{type:"primary",onClick:a[4]||(a[4]=t=>v(u.value))},{default:s(()=>[y("注册")]),_:1})]),_:1})]),_:1},8,["model"])]),_:1})),[[C,n.value]])])}}}),Z=G(O,[["__scopeId","data-v-e888aeb9"]]);export{Z as default};
