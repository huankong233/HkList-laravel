import{b as n,d as p,u as c,_ as i,w as u}from"./request-fdc4c150.js";import{a as d,e as o,c as t,n as r,u as s,h as l,i as y,p as m,j as f,q as h,z as b}from"./index-42a788ad.js";const v=n({header:{type:String,default:""},bodyStyle:{type:p([String,Object,Array]),default:""},bodyClass:String,shadow:{type:String,values:["always","hover","never"],default:"always"}}),C=d({name:"ElCard"}),S=d({...C,props:v,setup(_){const a=c("card");return(e,g)=>(o(),t("div",{class:r([s(a).b(),s(a).is(`${e.shadow}-shadow`)])},[e.$slots.header||e.header?(o(),t("div",{key:0,class:r(s(a).e("header"))},[l(e.$slots,"header",{},()=>[h(b(e.header),1)])],2)):y("v-if",!0),m("div",{class:r([s(a).e("body"),e.bodyClass]),style:f(e.bodyStyle)},[l(e.$slots,"default")],6)],2))}});var w=i(S,[["__file","/home/runner/work/element-plus/element-plus/packages/components/card/src/card.vue"]]);const N=u(w);export{N as E};
